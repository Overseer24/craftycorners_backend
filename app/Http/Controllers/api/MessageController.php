<?php

namespace App\Http\Controllers\api;

use App\Events\PublicChat;
use App\Http\Controllers\Controller;
use App\Http\Requests\Message\StoreMessageRequest;

use App\Http\Resources\Message\ConversationsListResource;
use App\Http\Resources\Message\MessageResource;
use App\Http\Resources\Message\SpecificConversationResource;
use App\Models\Conversation;
use Illuminate\Http\Request;
Use App\Events\MessageSent;
use App\Models\Message;

class MessageController extends Controller
{
    public function sendMessage(StoreMessageRequest $request, $receiver_id, Message $message)
    {
      $user = auth()->id();

        $conversation = Conversation::
              whereIn('sender_id', [$user, $receiver_id])
            ->whereIn('receiver_id', [$user, $receiver_id])
            ->first();

      if(!$conversation){
          $conversation = Conversation::create([
              'sender_id' => $user,
              'receiver_id' => $receiver_id
          ]);
      }
      $message = $message->create([
          'conversation_id' => $conversation->id,
          'sender_id' => $user,
          'receiver_id' =>$receiver_id,
          'message' => $request->message,
          'read' => false
      ]);

//        $messageResource = new MessageResource($message, $user);
      broadcast(new MessageSent($user,new MessageResource($message), $conversation))->toOthers();
      return new MessageResource($message);
//       return response()->json($messageResource);
    }
    public function getMessages($receiver_id)
    {

    }
    //get all users conversations
    public function getConversations()
    {
//        $user = auth()->user();
//        $conversations = $user->conversations()
//            ->with(['receiver', 'messages'=> function ($query){$query->latest()->first();}])->get();


        $user = auth()->user();



        $conversations = $user->conversations()
            ->with(['messages' => function ($query) {
                $query->latest()->take(1);
            }]) ->get();

        //list all conversations related to the user

        return ConversationsListResource::collection($conversations);
    }
    //when user open a specific conversation
    public function getConversation($conversation_id)
    {
        $user = auth()->user();

        $conversation = $user->conversations()->where('id', $conversation_id)
            ->with(['receiver:id,first_name,last_name',
                'messages'=> function ($query){$query->latest();}])
            ->first();
        return new SpecificConversationResource($conversation);
    }

   public function markAsRead($conversation_id)
   {
       $user = auth()->user();
       $conversation = Conversation::find($conversation_id);
       $conversation->messages()->where('user_id', '!=', $user->id)->update(['read' => true]);
       return response()->json(['message' => 'success']);
   }


}
