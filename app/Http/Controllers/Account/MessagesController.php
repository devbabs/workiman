<?php

namespace App\Http\Controllers\Account;

use App\Conversation;
use App\ConversationMessage;
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
        $conversations = $user->conversations;

        // foreach ($conversations as $conversation) {
        //     # code...
        //     if($username == null){
        //         $user2 = auth()->user();
        //     }else{
        //         $user2 = $conversation->user_1_id;
        //     }
        // }

        $unread_messages = ConversationMessage::where('user_id', $user->id)->orderBy('updated_at', 'desc')->where('read', false)->get();
        // foreach ($unread_messages as $message) {
        //     # code...
        //     $message->read = true;
        //     $message->save();
        // }

        // dd($conversations);

        return view('account.conversations', compact('user', 'user_2', 'conversations'));
    }

    public function sendMessage(Request $request)
    {
        try {
            $this->validate($request, [
                "user_id" => "bail|required|exists:users,id",
                "message" => "bail|required|string"
            ]);

            $user = auth()->user();
            $user_2 = User::find($request->user_id);
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
            $message->receiver_id = $user_2->id;
            $message->save();

            try {
                // $freelancer = User::find($offer->offer_user_id);
                $reciever = $conversation->user_2;
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
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}