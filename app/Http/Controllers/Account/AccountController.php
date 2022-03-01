<?php

namespace App\Http\Controllers\Account;

use App\Bank;
use App\Contest;
use App\ContestDispute;
use App\Country;
use App\Freelancer;
use App\FreelancerOfferDispute;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\DisputeNotification;
use App\Notification;
use App\PaymentMethod;
use App\ProjectManagerOffer;
use App\ProjectManagerOfferDispute;
use App\User;
use App\Withdrawal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware(['account', 'verified'])->except('profile');
    }

    public function dashboard()
    {
        $user = auth()->user();
        // foreach ($user->new_contests as $value) {
        //     # code...
        //     if($value->status == 'active'){
        //         dd($value);
        //     }
        //     // dd($value->status);
        // }
        // dd($user->new_contests);

        $countries = Country::get();
        $suggested_contests = [];

        $user_location_currency = getCurrencyFromLocation();

        if ($user->freelancer) {
            $suggested_contests = Contest::whereHas('payment')->where(function ($query) use ($user) {
                $query->whereHas('submissions', function ($submission) use ($user) {
                    $submission->whereDoesntHave('user', function ($submission_user) use ($user) {
                        $submission_user->where('id', $user->id);
                    });
                })->orWhereDoesntHave('submissions');
            })
                ->whereNull("ended_at")->whereNotNull("ends_at")->where("ends_at", ">", now())
                ->orderBy('created_at', 'desc')->take(3)->get();
        }

        return view('account.dashboard', compact('user', 'countries', 'suggested_contests', 'user_location_currency'));
    }

    public function wallet()
    {
        $user = auth()->user();
        $withdrawals = $user->withdrawals;
        $naira_banks = Bank::get();
        $dollar_banks = [
            [
                'name' => 'Skrill',
                'key' => 'skrill'
            ],
            [
                'name' => 'PayPal',
                'key' => 'paypal'
            ],
        ];

        return view('account.wallet', compact('user', 'withdrawals', 'naira_banks', 'dollar_banks'));
    }

    public function walletWithdrawal(Request $request)
    {
        try {
            $user = auth()->user();

            $this->validate($request, [
                'currency' => 'bail|required|in:ngn,usd',
            ]);

            $exchange_rate = 500;
            $wallet_balance_limit = $user->wallet_balance * ($request->currency == "usd" ? 1 : $exchange_rate);

            $this->validate($request, [
                'amount' => "bail|required|numeric|min:0|max:{$wallet_balance_limit}",
            ], [
                'amount.max' => "You do not have sufficient balance."
            ]);

            Log::info($request->all());
            // dd($wallet_balance_limit);


            $this->validate($request, [
                'bank' => 'bail|required',
                'account_name' => 'bail|required',
            ], [
                'account_name.required' => $request->currency == "usd" ? "Please enter a valid email address." : "Account name is required."
            ]);

            if ($request->currency == 'ngn') {
                $this->validate($request, [
                    'bank' => 'exists:banks,id',
                    'account_number' => 'bail|required',
                ]);
            } else {
            }

            $reference = '';

            for ($i = 0; $i < 8; $i++) {
                $reference .= rand(0, 9);
            }

            $withdrawal = new Withdrawal();
            $withdrawal->user_id = $user->id;
            $withdrawal->amount = $request->amount;
            $withdrawal->fx_rate = $request->currency == "usd" ? 1 : $exchange_rate;
            $withdrawal->reference = $reference;
            $withdrawal->currency = $request->currency;
            $withdrawal->bank_name = $request->currency == "usd" ? $request->bank : Bank::find($request->bank)->name;
            $withdrawal->bank_id = $request->currency == "usd" ? null : $request->bank;
            $withdrawal->account_name = $request->account_name;
            $withdrawal->account_number = $request->account_number;
            $withdrawal->save();

            return response()->json([
                'message' => 'Your withdrawal request has been saved successfully',
            ]);
        } catch (ValidationException $exception) {
            return response()->json([
                'message' => $exception->validator->errors()->first()
            ], 422);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function verifyAccountNumber(Request $request)
    {
        try {
            $user = auth()->user();

            $this->validate($request, [
                'bank' => 'bail|required|exists:banks,id',
                'account_number' => 'bail|required|string',
            ]);

            $bank = Bank::find($request->bank);

            $verifyAccount = Http::withHeaders([
                "Authorization" => "Bearer sk_test_8a50ca8cb969f47290efb4f2dc78f2165c8ea63d"
            ])->get("https://api.paystack.co/bank/resolve?account_number={$request->account_number}&bank_code={$bank->code}");

            if ($verifyAccount->status() == 200) {
                return response()->json([
                    'message' => 'Account number verified successfully',
                    'account_name' => json_decode(json_encode($verifyAccount->json()))->data->account_name
                ]);
            }

            throw new \Exception("Invalid Account Number.", 1);
        } catch (ValidationException $exception) {
            return response()->json([
                'message' => $exception->validator->errors()->first()
            ], 422);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function settings(Request $request)
    {
        try {
            $user = auth()->user();
            $countries = Country::get();
            $banks = Bank::get();

            // dd($request);

            if ($request->isMethod('put')) {
                switch ($request->setting) {
                    case 'basic':
                        $this->validate($request, [
                            'first_name' => 'bail|required|string',
                            'last_name' => 'bail|required|string',
                            'username' => 'bail|required|string',
                            'country' => 'bail|required|string',
                            'phone' => 'bail|required|string',
                            'about' => 'bail|required|string',
                        ]);

                        if (!$country = Country::where('id', $request->country)->first()) {
                            throw new \Exception("Invalid Country", 1);
                        }

                        if ($request->phone != $user->phone) {
                            $this->validate($request, [
                                'phone' => 'unique:users,phone,' . $user->phone . ',id'
                            ]);
                        }

                        $user->first_name = $request->first_name;
                        $user->last_name = $request->last_name;
                        $user->username = $request->username;
                        $user->country_id = $country->id;
                        $user->phone = $request->phone;
                        $user->about = $request->about;

                        if (!$request->hasFile('avatar') && !$user->avatar) {
                            throw new \Exception("Please add a valid profle picture", 1);
                        } elseif ($request->hasFile('avatar')) {
                            $avatar = $request->file('avatar');
                            $user->avatar = Str::random(10) . '.' . $avatar->getClientOriginalExtension();
                            Storage::putFileAs('public/avatars', $avatar, $user->avatar);
                        }

                        // dd($user);

                        $user->save();

                        return redirect()->route('account.settings')->with('success', 'Profile saved successfully');

                        break;
                    case 'skills':
                        $this->validate($request, [
                            'skills' => 'bail|required|string',
                            // 'awards' => 'bail|required|string',
                            // 'portfolio' => 'bail|required|string',
                            // 'social_media' => 'bail|required|string'
                        ]);

                        if (!$freelancer_profile = Freelancer::where('user_id', $user->id)->first()) {
                            $freelancer_profile = new Freelancer();
                            $freelancer_profile->user_id = $user->id;
                        }

                        // $freelancer_profile->portfolio = $request->portfolio;
                        // $freelancer_profile->social_media = $request->social_media;
                        $freelancer_profile->portfolio = '#';
                        $freelancer_profile->social_media = '#';
                        $freelancer_profile->skills = $request->skills;
                        $freelancer_profile->awards = $request->awards;

                        // if (!$request->hasFile('cover_letter') && !$freelancer_profile->cover_letter) {
                        //     throw new \Exception("Please add a cover_letter", 1);
                        // } else
                        // if ($request->hasFile('cover_letter')) {
                        //     $cover_letter = $request->file('cover_letter');
                        //     $freelancer_profile->cover_letter = Str::random(10) . '.' . $cover_letter->getClientOriginalExtension();
                        //     Storage::putFileAs('public/cover-letters', $cover_letter, $freelancer_profile->cover_letter);
                        // }

                        $freelancer_profile->save();

                        return redirect()->route('account.settings')->with('success', 'Profile saved successfully');

                        break;
                    case 'payment':
                        $this->validate($request, [
                            'payment_method' => 'bail|required|string'
                        ]);

                        if (is_null($user->payment_method)) {
                            $user->payment_method = new PaymentMethod();
                            $user->payment_method->user_id = $user->id;
                        }

                        switch ($request->payment_method) {
                            case 'bank':
                                $this->validate($request, [
                                    'bank' => 'bail|required|string',
                                    'account_number' => 'bail|required|string',
                                    'account_name' => 'bail|required|string',
                                ]);
                                $user->payment_method->bank = $request->bank;
                                $user->payment_method->account_number = $request->account_number;
                                $user->payment_method->account_name = $request->account_name;
                                $user->payment_method->email = null;
                                break;
                            default:
                                $this->validate($request, [
                                    'payment_email' => 'bail|required|email',
                                ]);
                                $user->payment_method->email = $request->payment_email;
                                $user->payment_method->bank = null;
                                $user->payment_method->account_number = null;
                                $user->payment_method->account_name = null;
                                # code...
                                break;
                        }

                        $user->payment_method->method = $request->payment_method;

                        $user->payment_method->save();

                        return redirect()->route('account.settings')->with('success', 'Profile saved successfully');

                        break;
                    case 'password':

                        if (!auth()->attempt([
                            'email' => auth()->user()->email,
                            'password' => $request->password_old
                        ])) {
                            throw new \Exception("Invalid Password", 1);
                        }

                        $this->validate($request, [
                            'password_old' => 'bail|required|string',
                            'password' => 'bail|required|confirmed',
                        ]);

                        $user->password = bcrypt($request->password);

                        $user->payment_method->save();

                        return redirect()->route('account.settings')->with('success', 'Profile saved successfully');

                        break;
                    default:
                        # code...
                        break;
                }

                // dd($request);

                throw new \Exception("Invalid Profile Modification", 1);
            }

            return view('account.settings', compact('user', 'countries', 'banks'));
        } catch (ValidationException $exception) {
            // dd($exception->validator->errors()->first());
            return back()->with('danger', $exception->validator->errors()->first());
        } catch (\Exception $exception) {
            // dd($exception->getMessage());
            return back()->with('danger', $exception->getMessage());
        }
    }

    public function profile($username = null)
    {
        if ($username) {
            $user = User::where('username', $username)->first();
        } else {
            if (auth()->check())
                $user = auth()->user();
            else
                return redirect()->route('index')->with('danger', 'Please sign in to continue');
        }

        if ($user) {

            // dd($user->response_rate);

            return view('account.profile', compact('user'));
        }

        abort(404, "Invalid User");
    }

    public function hold_contest(Request $request)
    {
        try {
            $this->validate($request, [
                'contest' => 'bail|required|string',
            ]);
            $dispute = ContestDispute::where('contest_id', $request->contest)->first();
            if ($dispute ==  null) {
                $dispute = new ContestDispute();
                $dispute->contest_id = $request->contest;
                $dispute->comments = $request->comments;
                $dispute->save();
                try {
                    $sender = auth()->user();
                    $reciever = $dispute->contest->user;
                    Mail::to($reciever->email)
                        ->cc($reciever->email)
                        ->bcc('kikioyeniran@gmail.com')
                        ->send(new DisputeNotification($dispute->id, 'contest', $sender->id, $reciever->id));
                    Log::alert("email sent sucessfully for to {$reciever->email}");

                    $notification = new Notification();
                    $notification->contest_id = $request->contest;
                    $notification->user_id = $dispute->contest->user;
                    $notification->message = "You just created a dispute for the contest with title" . $dispute->contest->title;
                    $notification->save();

                    $contest = Contest::find($request->contest);
                    if (count($contest->submissions) > 0) {
                        foreach ($contest->submissions as $key => $submission) {
                            # code...
                            $notification = new Notification();
                            $notification->contest_id = $request->contest;
                            $notification->user_id = $submission->user_id;
                            $notification->message = "A dispute was created for the contest with title" . $dispute->contest->title . "by" . $contest->user->username;
                            $notification->save();
                        }
                    }
                } catch (\Throwable $th) {
                    Log::alert("email for new chat with to {$reciever->email} failed to send due to " . $th->getMessage());
                }
            } elseif ($dispute != null && $dispute->resolved == true) {
                $dispute->resolved = false;
                $dispute->comments = $request->comments ? $request->comments : $dispute->comments;
                $dispute->save();
            } else {
                return back()->with('danger', 'This Contest is already on hold');
            }

            return back()->with('success', 'Contest Put on Hold');

            // throw new \Exception("Invalid Category", 1);
        } catch (\Exception $exception) {
            return back()->with('danger', $exception->getMessage());
        }
    }

    public function hold_project_manager_offer(Request $request)
    {
        try {
            $this->validate($request, [
                'offer' => 'bail|required|string',
            ]);
            $dispute = ProjectManagerOfferDispute::where('project_manager_offer_id', $request->offer)->first();
            if ($dispute ==  null) {
                $dispute = new ProjectManagerOfferDispute();
                $dispute->project_manager_offer_id = $request->offer;
                $dispute->comments = $request->comments;
                $dispute->save();
                try {
                    $sender = auth()->user();
                    $reciever = $dispute->project_manager_offer->user;
                    Mail::to($reciever->email)
                        ->cc($reciever->email)
                        ->bcc('kikioyeniran@gmail.com')
                        ->send(new DisputeNotification($dispute->id, 'project_manager_offer', $sender->id, $reciever->id));
                    Log::alert("email sent sucessfully for to {$reciever->email}");
                } catch (\Throwable $th) {
                    Log::alert("email for new chat with to {$reciever->email} failed to send due to " . $th->getMessage());
                }
                // dd($dispute);
            } elseif ($dispute != null && $dispute->resolved == true) {
                $dispute->resolved = false;
                $dispute->comments = $request->comments ? $request->comments : $dispute->comments;
                $dispute->save();
            } else {
                return back()->with('danger', 'This Offer is already on hold');
            }

            return back()->with('success', 'Offer Put on Hold');

            // throw new \Exception("Invalid Category", 1);
        } catch (\Exception $exception) {
            return back()->with('danger', $exception->getMessage());
        }
    }

    public function hold_freelancer_offer(Request $request)
    {
        try {
            $this->validate($request, [
                'offer' => 'bail|required|string',
            ]);
            $dispute = FreelancerOfferDispute::where('freelancer_offer_id', $request->offer)->first();
            if ($dispute ==  null) {
                $dispute = new FreelancerOfferDispute();
                $dispute->freelancer_offer_id = $request->offer;
                $dispute->comments = $request->comments;
                $dispute->save();
                try {
                    $sender = auth()->user();
                    $reciever = $dispute->freelancer_offer->user;
                    Mail::to($reciever->email)
                        ->cc($reciever->email)
                        ->bcc('kikioyeniran@gmail.com')
                        ->send(new DisputeNotification($dispute->id, 'freelancer_offer', $sender->id, $reciever->id));
                    Log::alert("email sent sucessfully for to {$reciever->email}");
                } catch (\Throwable $th) {
                    Log::alert("email for new chat with to {$reciever->email} failed to send due to " . $th->getMessage());
                }
            } elseif ($dispute != null && $dispute->resolved == true) {
                $dispute->resolved = false;
                $dispute->comments = $request->comments ? $request->comments : $dispute->comments;
                $dispute->save();
            } else {
                return back()->with('danger', 'This Offer is already on hold');
            }

            return back()->with('success', 'Offer Put on Hold');

            // throw new \Exception("Invalid Category", 1);
        } catch (\Exception $exception) {
            return back()->with('danger', $exception->getMessage());
        }
    }
}
