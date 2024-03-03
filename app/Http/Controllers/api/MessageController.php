<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use App\Events\MessageSent;
use App\Models\Message;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'to_user_id' => 'required|exists:users,id', // check if the receiver exists in the users table
            'message' => 'required'
        ]);

        $message = Message::create([
            'from_user_id' => $user->id,
            'to_user_id' => $request->to_user_id,
            'message' => $request->message
        ]);

        broadcast(new MessageSent($message))->toOthers();

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
