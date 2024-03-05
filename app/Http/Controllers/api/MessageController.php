<?php

namespace App\Http\Controllers\api;

use App\Events\PublicChat;
use App\Http\Controllers\Controller;
use App\Http\Requests\Message\StoreMessageRequest;
use App\Http\Resources\Message\MessageResource;
use Illuminate\Http\Request;
Use App\Events\MessageSent;
use App\Models\Message;

class MessageController extends Controller
{
    public function sendMessage(StoreMessageRequest $request, $receiver_id)
    {
        $user = auth()->user();

    $message = Message::create([
            'from_user_id' =>$user->id,
            'to_user_id' => $receiver_id,
            'message' => $request->message
        ]);
    $message->load('sender','receiver');
        broadcast(new MessageSent(new MessageResource($message)))->toOthers();

        return new MessageResource($message);
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
