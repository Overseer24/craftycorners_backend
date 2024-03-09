<?php

namespace App\Http\Controllers\api;

use App\Events\PublicChat;
use App\Http\Controllers\Controller;
use App\Http\Requests\Message\StoreMessageRequest;

use App\Http\Resources\Message\ConversationsListResource;
use App\Http\Resources\Message\MessageResource;
use App\Http\Resources\Message\SpecificConversationResource;
use App\Models\Conversation;
use http\Env\Response;
use Illuminate\Http\Request;
Use App\Events\MessageSent;
use App\Models\Message;

class MessageController extends Controller
{

    public function startAConversation($receiver_id)
    {
        $user = auth()->id();

        //if conversation already been established redirect to other function
        $conversation = Conversation::
        whereIn('sender_id', [$user, $receiver_id])
            ->whereIn('receiver_id', [$user, $receiver_id])
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'sender_id' => $user,
                'receiver_id' => $receiver_id
            ]);
        }
        //check if there is a message in their convo if non proceed to request


//        if (!$request->has('message')) {
//            $this->deleteEmptyConversation($conversation);
//            return response()->json(['message' => 'no message'], 400);
//        }
        return new SpecificConversationResource($conversation);
    }

    public function deleteEmptyConversation($conversation){
        $conversation = Conversation::find($conversation);
        if($conversation->messages->isEmpty()){
            $conversation->delete();
        }
        else{
            return response()->json(['message' => 'there are messages in this conversation'], 400);
        }
    }

    public function sendMessage(Request $request, $conversation_id, $receiver_id)
    {
        $user = auth()->id();
        $this->validate($request, [
            'message' => 'required'
        ]);
        //update or create the message either being called in the function or in the route

        $message = Message::Create([
            'conversation_id' => $conversation_id,
            'sender_id' => $user,
            'receiver_id' => $receiver_id,
            'message' => $request->message,
            'read' => false
        ]);


        $conversation = Conversation::find($conversation_id);
        broadcast(new MessageSent($user, new MessageResource($message), $conversation))->toOthers();
        return new MessageResource($message);
    }

    public function getConversation($receiver_id)
    {
        $user = auth()->id();
        //fetch messages ordered by latest
        $conversation = Conversation::where(function ($query) use ($user, $receiver_id) {
            $query->where('sender_id', $user)->where('receiver_id', $receiver_id);
        })->orWhere(function ($query) use ($user, $receiver_id) {
            $query->where('sender_id', $receiver_id)->where('receiver_id', $user);
        })
            ->with(['messages'=> function ($query) use ($user){
                $query->latest();
            }])
            ->first();
        $conversation->load(['sender', 'receiver', ]);
        if (!$conversation) {
            return response()->json(['message' => 'no conversation found'], 404);
        }
        //paginate the messages
//        $messages = $conversation->messages()->latest()->paginate(10);
//
//        $conversation->setRelation('messages', $messages);

        return new SpecificConversationResource($conversation);
    }

    public function getConversations(){
        $user = auth()->id();
        $conversations = Conversation::where('sender_id', $user)
            ->orWhere('receiver_id', $user)
            ->with(['messages' => function ($query) {
                $query->latest()->take(1);
            }])
            ->get();

        return ConversationsListResource::collection($conversations);

    }

    public function markAsRead($conversation_id)
    {
        $user = auth()->id();
        $message = new Message();
        $message->markAsread($conversation_id, $user);

        return response()->json(['message' => 'success']);
}
}
//    public function getMessages($receiver_id)
//    {
//
//
    //get all users conversations
//    public function getConversations()
//    {
////        $user = auth()->user();
////        $conversations = $user->conversations()
////            ->with(['receiver', 'messages'=> function ($query){$query->latest()->first();}])->get();
//
//
//        $user = auth()->user();
//
//        $conversations = $user->conversations()
//            ->with(['messages' => function ($query) {
//                $query->latest()->take(1);
//            }]) ->get();
//
//        //list all conversations related to the user
//
//        return ConversationsListResource::collection($conversations);
//    }
//    //when user open a specific conversation
//    public function getConversation($conversation_id)
//    {
//
//    }
//
//   public function markAsRead($conversation_id)
//   {
//       $user = auth()->user();
//       $conversation = Conversation::find($conversation_id);
//       $conversation->messages()->where('user_id', '!=', $user->id)->update(['read' => true]);
//       return response()->json(['message' => 'success']);
//   }
//
//
//}
