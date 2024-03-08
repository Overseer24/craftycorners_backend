<?php

namespace App\Http\Controllers\api;

use App\Events\PublicChat;
use App\Http\Controllers\Controller;
use App\Http\Requests\Message\StoreMessageRequest;

use App\Http\Resources\Message\ConversationsListResource;
use App\Http\Resources\Message\MessageResource;
use App\Models\Conversation;
use Illuminate\Http\Request;
Use App\Events\MessageSent;
use App\Models\Message;

class MessageController extends Controller
{
    public function sendMessage(StoreMessageRequest $request, $receiver_id, Message $message)
    {
      $user = auth()->user();

        $conversation = Conversation::
              whereIn('sender_id', [$user->id, $receiver_id])
            ->whereIn('receiver_id', [$user->id, $receiver_id])
            ->first();

      if(!$conversation){
          $conversation = Conversation::create([
              'sender_id' => $user->id,
              'receiver_id' => $receiver_id
          ]);
      }

      $message = $message->create([
          'conversation_id' => $conversation->id,
          'user_id' => $user->id,
          'message' => $request->message
      ]);

        $conversation->update(['read' => false]);

        broadcast(new MessageSent($message, $conversation))->toOthers();

        return response()->json(['message' => new MessageResource($message)]);
    }

    public function getMessages($receiver_id)
    {

    }
    public function getConversations()
    {
        $user = auth()->user();
        $conversations = $user->conversations()->with(['receiver:id,first_name,last_name', 'messages'=> function ($query){
            $query->latest()->first();
        }])->get();

        //list all conversations related to the user
        return ConversationsListResource::collection($conversations);
    }


}
