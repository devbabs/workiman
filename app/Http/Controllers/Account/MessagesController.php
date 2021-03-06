<?php

namespace App\Http\Controllers\Account;

use App\Conversation;
use App\ConversationMessage;
use App\Http\Controllers\actions\UtilitiesController;
use App\Http\Controllers\Controller;
use App\Mail\NewMessageChat;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class MessagesController extends Controller
{
    public function index($username = null)
    {
        $user = auth()->user();
        $user_2 = User::where('username', $username)->count() ? User::where('username', $username)->first() : null;
        // dd($user_2);
        $conversations = $user->conversations;

        foreach ($conversations as $conversation) {
            # code...
            $messages = $conversation->messages;
            foreach ($messages as $message) {
                # code...
                if($message->user_id != $user->id){
                    $message->read = true;
                    $message->save();
                }
            }
        }

        // $unread_messages = ConversationMessage::where('user_id', $user->id)->orderBy('updated_at', 'desc')->where('read', false)->get();

        return view('account.conversations', compact('user', 'user_2', 'conversations'));
    }

    public function sendMessage(Request $request)
    {
        try {
            $this->validate($request, [
                "user_id" => "bail|required|exists:users,id",
                "message" => "bail|nullable|string",

            ]);

            $user = auth()->user();
            $user_2 = User::find($request->user_id);
            Log::alert($user);
            Log::alert($user_2);
            $content_type = $request->content_type or "text";

            if ($user->id == $user_2->id) {
                throw new \Exception("You can not send a message to yourself.", 1);
            }

            if (!$conversation = Conversation::where(function ($query) use ($user, $user_2) {
                $query->where('user_1_id', $user->id)->where('user_2_id', $user_2->id);
            })->orWhere(function ($query) use ($user, $user_2) {
                $query->where('user_2_id', $user->id)->where('user_1_id', $user_2->id);
            })->first()) {
                $conversation = new Conversation();
                $conversation->user_1_id = $user->id;
                $conversation->user_2_id = $user_2->id;
                $conversation->save();
            }

            // Save Message
            $message = new ConversationMessage();
            $message->conversation_id = $conversation->id;
            $message->content_type = $content_type;
            $message->content = $request->message;
            $message->user_id = $user->id;
            // $message->receiver_id = $user_2->id;
            $message->save();

            try {
                // $freelancer = User::find($offer->offer_user_id);
                $reciever = $user_2;
                Mail::to($reciever->email)
                ->send(new NewMessageChat($message->id, $reciever->id));
                Log::alert("email sent sucessfully for to {$reciever->email}");
            } catch (\Throwable $th) {
                Log::alert("email for new chat with to {$reciever->email} failed to send due to " . $th->getMessage());
            }

            return response()->json([
                'message' => "Message sent successfully.",
                'conversations' => $user->conversations
            ]);
        } catch (ValidationException $th) {

            return response()->json([
                'message' => $th->validator->errors()->first()
            ], 422);
        } catch (\Throwable $th) {
            Log::alert($th->getMessage());
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function sendFile(Request $request){
        // Log::alert($request->all());
        try {
            $this->validate($request, [
                "user_id" => "bail|required|exists:users,id",
            ]);

            Log::alert($request->all());

            $user = auth()->user();
            $user_2 = User::find($request->user_id);
            $content_type = "file";

            Log::alert($user);
            Log::alert($user_2);

            if ($user->id == $user_2->id) {
                throw new \Exception("You can not send a message to yourself.", 1);
            }

            if (!$conversation = Conversation::where(function ($query) use ($user, $user_2) {
                $query->where('user_1_id', $user->id)->where('user_2_id', $user_2->id);
            })->orWhere(function ($query) use ($user, $user_2) {
                $query->where('user_2_id', $user->id)->where('user_1_id', $user_2->id);
            })->first()) {
                $conversation = new Conversation();
                $conversation->user_1_id = $user->id;
                $conversation->user_2_id = $user_2->id;
                $conversation->save();
            }

            if($request->hasFile('document')){
                foreach ($request->file('document') as $submission_file) {

                    $image = $submission_file;
                    $call = new UtilitiesController();
                    $fileNameToStore = $call->fileNameToStore($image);
                    // $file_name = $fileNameToStore;

                    // Save Message
                    $message = new ConversationMessage();
                    $message->conversation_id = $conversation->id;
                    $message->content_type = $content_type;
                    $message->content = $fileNameToStore;
                    $message->user_id = $user->id;
                    $message->save();
                }
            }

            try {
                // $freelancer = User::find($offer->offer_user_id);
                $reciever = $user_2;
                Mail::to($reciever->email)
                ->send(new NewMessageChat($message->id, $reciever->id));
                Log::alert("email sent sucessfully for to {$reciever->email}");
            } catch (\Throwable $th) {
                Log::alert("email for new chat with to {$reciever->email} failed to send due to " . $th->getMessage());
            }

            return response()->json([
                'message' => "Message sent successfully",
                'conversations' => $user->conversations
            ]);
        } catch (ValidationException $th) {
            Log::alert($th->validator->errors()->first());
            return response()->json([
                'message' => $th->validator->errors()->first()
            ], 422);
        } catch (\Throwable $th) {
            Log::alert($th->getMessage());
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}