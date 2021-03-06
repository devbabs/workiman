<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    // public $appends = [
    //     'display_name'
    // ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getDisplayNameAttribute()
    {
        return trim($this->full_name) != "" ? $this->full_name : $this->username;
    }

    public function contests()
    {
        return $this->hasMany(Contest::class);
    }

    public function getPaidContestsAttribute()
    {
        return $this->hasMany(Contest::class)->whereHas('payment');
    }

    public function getCountryAttribute()
    {
        return Country::where('id', $this->country_id)->first();
    }

    public function freelancer_profile()
    {
        return $this->hasOne(Freelancer::class);
    }

    public function payment_method()
    {
        return $this->hasOne(PaymentMethod::class);
    }

    public function project_manager_offers()
    {
        return $this->hasMany(ProjectManagerOffer::class)->with(['sub_category.offer_category', 'user']);
    }

    public function getPaidProjectManagerOffersAttribute()
    {
        return $this->hasMany(ProjectManagerOffer::class)->with('sub_category.offer_category')->whereHas('payment');
    }

    public function freelancer_offers()
    {
        return $this->hasMany(FreelancerOffer::class)->with(['sub_category.offer_category', 'user']);
    }

    public function contest_submissions()
    {
        return $this->hasMany(ContestSubmission::class);
    }

    public function project_manager_offer_interests()
    {
        return $this->hasMany(ProjectManagerOfferInterest::class);
    }

    public function getConversationsAttribute()
    {
        return Conversation::where('user_1_id', $this->id)->orWhere('user_2_id', $this->id)->get();
    }

    public function getFreelancerRankAttribute()
    {
        $rank = 0;

        if ($this->freelancer) {
            // Calculate Rank
            $rank = 1;
        }

        return $rank;
    }

    public function getWalletBalanceAttribute()
    {
        $balance = 0;


        if ($this->freelancer) {
            // Add total income from contest submissions
            foreach ($this->completed_contest_submissions as $completed_contest_submission) {
                Log::alert('contest position' . $completed_contest_submission->position);
                Log::alert('contest prize money' . $completed_contest_submission->contest->prize_money[$completed_contest_submission->position]);
                $balance += $this->calculateBalance($completed_contest_submission->contest->prize_money[$completed_contest_submission->position], $completed_contest_submission->contest->currency);
                Log::alert("balance 1 dump" . $balance);
            }

            // dd($this->completed_offers[0]);
            // Add total income from completed offers
            foreach ($this->completed_offer_interests as $completed_offer_interest) {
                Log::alert('offer prize money' . $completed_offer_interest->offer->prize_money);
                $balance += $this->calculateBalance($completed_offer_interest->offer->prize_money, $completed_offer_interest->offer->currency);
                Log::alert($balance);
            }

            // Subtract withdrawals
            foreach ($this->withdrawals->where('status', '!=', 'rejected') as $withdrawal) {
                $balance -= ($withdrawal->amount / $withdrawal->fx_rate);
                // dd($withdrawal->amount);
                // dd($withdrawal->fx_rate);
            }
            Log::alert($balance);
        }

        return $balance;
    }

    public function getCurrencyAttribute()
    {
        $user_currency = "";
        if ($this->country_id == 566) {
            $user_currency = 'naira';
        } else {
            $user_currency = 'dollar';
        }
        return $user_currency;
    }

    public function calculateBalance($amount, $currency)
    {
        $user_currency = $this->currency;
        // $destination_currency = "";
        $dollar_rate = Session::get('dollar_rate');
        $balance = 0;
        Log::alert('user currency ' .  $user_currency);
        Log::alert('currency ' .  $currency);
        Log::alert('amount ' .  $amount);
        Log::alert('dollar_rate ' . $dollar_rate);
        if ($user_currency == $currency) {
            $balance = $amount;
        } elseif ($currency == 'naira' && $user_currency == 'dollar') {
            // dd($amount);
            $balance = $amount / $dollar_rate;
        } else {
            $balance = $amount * $dollar_rate;
        }
        return $balance;
    }

    public function getCompletedContestSubmissionsAttribute()
    {
        return $this->contest_submissions->whereNotNull('position')->where('completed', true);
    }

    public function getCompletedOfferInterestsAttribute()
    {
        return ProjectManagerOfferInterest::where('user_id', $this->id)->where('assigned', true)->whereHas('offer', function ($offer) {
            $offer->where('completed', true);
        })->get();
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function getJobSuccessAttribute()
    {
        $job_success = 100;

        if ($this->freelancer) {
        }

        return $job_success;
    }

    public function getResponseRateAttribute()
    {
        $response_rate = 100;
        $average_lag_time = 0;
        $user = $this;
        $hourly_percentage_score = 5;
        $total_conversations_counted = [];

        // Check for conversations this user is involved in that were not started by this user
        $conversations = Conversation::where(function ($query) use ($user) {
            $query->where('user_1_id', $user->id)->orWhere('user_2_id', $user->id);
        })->get();
        // ->whereHas('messages', function($query) use ($user) {
        //     // $query->where('user_id', '!=', $user->id);
        // });

        // var_dump($user->id);

        foreach ($conversations as $key => $conversation) {
            if (ConversationMessage::where('conversation_id', $conversation->id)->orderBy('created_at', 'asc')->first()->user_id == $user->id) {
                $conversations->forget($key);
            } else {
                // Check time first message was sent
                $first_message_received_time = ConversationMessage::where('conversation_id', $conversation->id)->orderBy('created_at', 'asc')->where('user_id', '!=', $this->id)->first()->created_at;
                $first_message_response_time = \Carbon\Carbon::now();

                if ($first_message_response = ConversationMessage::where('conversation_id', $conversation->id)->orderBy('created_at', 'asc')->where('user_id', $this->id)->first()) {
                    $first_message_response_time = $first_message_response->created_at;
                }

                $diff_in_hours = $first_message_response_time->diffInHours($first_message_received_time);

                // var_dump($first_message_received_time);
                // var_dump("__________________________________");
                // var_dump($first_message_response_time);

                // $total_conversations_counted += 1;
                $total_conversations_counted[] = $diff_in_hours < 24 ? ($hourly_percentage_score * $diff_in_hours) : 100;
            }
        }

        if (count($total_conversations_counted)) {
            $average_lag_time = array_sum($total_conversations_counted) / count($total_conversations_counted);
        }

        // dd($average_lag_time);
        // dd($total_conversations_counted);

        return $response_rate - $average_lag_time;
    }

    public function getNewOffersAttribute()
    {
        $offers = ProjectManagerOffer::where('offer_user_id', $this->id)->where('completed', false)->whereHas('payment')->whereDoesntHave('interests')->get();
        return $offers;
    }

    public function getNewContestsAttribute()
    {
        $contests = Contest::where(
            'ended_at',
            null
        )->get();
        return $contests;
    }

    public function getUnreadMessagesAttribute()
    {
        $count = 0;
        // $user = $this->user;
        $conversations = Conversation::where('user_1_id', $this->id)->orWhere('user_2_id', $this->id)->get();
        // $conversations = $user->conversations;

        foreach ($conversations as $conversation) {
            # code...
            $messages = $conversation->messages;
            foreach ($messages as $message) {
                # code...
                if ($message->user_id != $this->id && $message->read == false) {
                    $count++;
                }
            }
        }

        return $count;
    }

    public function getNotificationCountAttribute()
    {
        $count = 0;
        $new_offers = $this->new_offers;
        if ($this->freelancer) {
            $new_contests = $this->new_contests;
        } else {
            $new_contests = [];
        }
        $count = count($new_offers) + $count;
        foreach ($new_contests as $contest) {
            if ($contest->status == 'active') {
                $count++;
            }
        }

        return $count;
    }

    public function getIsNigeriaAttribute()
    {
        $is_nigeria = $this->country_id == 566 ? true : false;
        return $is_nigeria;
    }

    public function getIsUpdatedAttribute()
    {
        $is_updated = false;
        if ($this->country_id == null || $this->about == null || $this->phone == null) {
            $is_updated = false;
        } else {
            $is_updated = true;
        }
        return $is_updated;
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function getActiveNotificationsAttribute()
    {
        $notifications = Notification::where('user_id', $this->id)->where('read', false)->get();
        return $notifications;
    }
}
