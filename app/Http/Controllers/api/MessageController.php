<?php

namespace App\Http\Controllers\api;

use App\Events\PublicChat;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use App\Events\MessageSent;
use App\Models\Message;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {


        $request->validate([
           'from_user_id' => 'required|exists:users,id', // check if the sender exists in the users table
            'to_user_id' => 'required|exists:users,id', // check if the receiver exists in the users table
            'message' => 'required'
        ]);

       $message=  Message::create([
            'from_user_id' => $request->from_user_id,
            'to_user_id' => $request->to_user_id,
            'message' => $request->message
        ]);


        event(new PublicChat($message, $request->from_user_id));
        return response()->json(['status' => 'Message Sent!']);
    }

    public function getMessages($receiver_id)
    {
        $messages = Message::where('from_user_id', auth()->id())
            ->where('to_user_id', $receiver_id)
            ->orWhere('from_user_id', $receiver_id)
            ->where('to_user_id', auth()->id())
            ->get();
        return response()->json(['messages' => $messages]);
    }
}
