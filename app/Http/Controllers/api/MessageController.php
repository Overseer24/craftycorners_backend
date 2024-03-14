<?php

namespace App\Http\Controllers\api;

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

    public function startAConversation(int $receiver_id)
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
            return response()->json(['message' => 'Initialize Conversation',
            'conversation_id' => $conversation->id], 200);
        }
        return $this->getConversation($receiver_id);
    }

    public function deleteEmptyConversation($conversation)
    {
        $conversation = Conversation::find($conversation);
        if($conversation->messages->isEmpty()){
            $conversation->delete();
        }
        else{
            return response()->json(['message' => 'there are messages in this conversation'], 400);
        }
        return response()->json(['message' => 'success']);
    }

    public function sendMessage(StoreMessageRequest $request, $receiver_id)
    {
        $user = auth()->id();

        $request->validated();
        $conversation = Conversation::whereIn('sender_id', [$user, $receiver_id])
            ->whereIn('receiver_id', [$user, $receiver_id])->first();

        if(!$conversation){
            return response()->json(['message' => 'user does not belong in this conversation'], 400);
        }

        $message = Message::Create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user,
            'receiver_id' => $receiver_id,
            'message' => $request->message,
            'read' => false
        ]);

        broadcast(new MessageSent($user, new MessageResource($message)))->toOthers();
        return new MessageResource($message);
    }

    public function getConversation($receiver_id)
    {
        $user = auth()->id();
        //fetch messages ordered by latest
        $conversation = Conversation::with('receiver', 'sender')
            ->whereIn('sender_id', [$user, $receiver_id])
            ->whereIn('receiver_id', [$user, $receiver_id])->first();


        //check user 0
        if (!$conversation) {
            return response()->json(['message' => 'no conversation found'], 404);
        }
        $messages = $conversation->messages()->latest()->Paginate(10);
        $conversation->setRelation('messages', $messages);



        return new SpecificConversationResource($conversation);

    }

    public function getConversations(){
        $user = auth()->id();
        $conversations = Conversation::where('sender_id', $user)
            ->orWhere('receiver_id', $user)
            ->with('messages', 'receiver', 'sender')
            ->get();

//        $messages = $conversations->messages->latest()->paginate(10);
//        $conversations->setRelation('messages', $messages);


        return ConversationsListResource::collection($conversations);

    }

    public function markAsRead($conversation_id)
    {
        $user = auth()->id();
        $conversation = Conversation::find($conversation_id);

        //make sure that user is the receiver of the latest message before marking as read

        //check if the latest message received by the receiver is already read
        if ($conversation->messages->last()->read){
            return null;
        }
        if($conversation->messages->last()->receiver_id !== $user){
            return response()->json(['message' => 'you are not the receiver of the latest message'], 400);
        }else{
            $conversation->messages->last()->update(['read' => true]);
        }

        return response()->json(['message' => 'success']);
    }
}
