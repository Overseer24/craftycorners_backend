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
use Illuminate\Support\Facades\Cache;

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

        $latestNonDeletedMessage = $conversation->messageExcludingDeletedBy($user)->latest()->first();

        if ($latestNonDeletedMessage && $latestNonDeletedMessage->receiver_id == $user) {
            $latestNonDeletedMessage->markAsRead($conversation->id, $user);
            //clear cache
            Cache::forget('unreadMessagesCount-'.$user);
            $latestNonDeletedMessage->save();
        }

        $message = Message::Create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user,
            'receiver_id' => $receiver_id,
            'message' => encrypt($request->message), //encrypt the message
            'read' => false,
            'has_attachment' => $request->hasFile('attachment')
        ]);


        if($request->hasFile('attachment')){
            $file =$request->file('attachment');
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $timestamp = now()->format('Y-m-d-His');
            $newFilename = $filename.'-'.$timestamp.'.'.$extension;
            $filePath = $file->storeAs('messages/conversation_'.$conversation->id.'/attachments', $newFilename, 'public');
           $message->attachments()->create([
                'file_path' => $filePath,
                'file_name' => $newFilename, //added this line to store the filename in the database
                'file_type' => $file->getClientMimeType()
            ]);

        }
        $message->load('receiver', 'sender');



        Cache::forget('unreadMessagesCount-'.$receiver_id);
        broadcast(new MessageSent($user, new MessageResource($message)))->toOthers();
        return new MessageResource($message);
    }

    public function getConversation($receiver_id)
    {
        $user = auth()->id();
        //fetch messages ordered by latest
        $conversation = Conversation::with('receiver', 'sender')
            ->whereIn('sender_id', [$user, $receiver_id])
            ->whereIn('receiver_id', [$user, $receiver_id])
            ->first();


        //check user 0
        if (!$conversation) {
            return response()->json(['message' => 'no conversation found'], 404);
        }
//        $messages = $conversation->messages()->latest()->paginate(10);
        $messages = $conversation->messageExcludingDeletedBy($user)
            ->latest()
            ->with('attachments')
            ->paginate(10);
        $conversation->setRelation('messages', $messages);



        return new SpecificConversationResource($conversation);

    }

    public function getConversations(){
        $user = auth()->user();

        $conversations= $user->conversations()->with('messages', 'receiver', 'sender')
            ->whereHas('sender', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->whereHas('receiver', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->addSelect(['latest_message_at' => Message::select('created_at')
            ->whereColumn('conversation_id', 'conversations.id')
            ->latest()
            ->take(1)
        ])
            ->orderBy('latest_message_at', 'desc')
            ->get();;

        return ConversationsListResource::collection($conversations);

    }

    public function markAsRead($conversation_id)
    {
        $user = auth()->id();
        $conversation = Conversation::find($conversation_id);




        $latestNonDeletedMessage = $conversation->messageExcludingDeletedBy($user)->latest()->first();
        if ($latestNonDeletedMessage->read) {
            return null;
        }
        if ($latestNonDeletedMessage->receiver_id !== $user) {
            return response()->json(['message' => 'you are not the receiver of the latest message']);

        }
        else{
            $latestNonDeletedMessage->markAsRead($conversation_id, $user);
            Cache::forget('unreadMessagesCount-'.$user);
        }

        return response()->json(['message' => 'success']);

//        if ($conversation->messages->last()->read){
//            return null;
//        }
//        if($conversation->messages->last()->receiver_id !== $user){
//            return response()->json(['message' => 'you are not the receiver of the latest message'], 400);
//        }else{
//            $conversation->messages->last()->markAsRead($conversation_id, $user);
//            Cache::forget('unreadMessagesCount-'.$user);
//        }
//
//        return response()->json(['message' => 'success']);
    }

    public function deleteMessage($message_id, Message $message)
    {
        $user = auth()->id();
        $message = $message->find($message_id);

       //check first if the user is the sender or receiver of the message
        if($message->sender_id !== $user && $message->receiver_id !== $user){
            return response()->json(['message' => 'you are not the sender or receiver of this message'], 400);
        }

        //check if the message is already deleted by other user then permanently delete the message
        if($message->deleted_by !== null){
            $message->delete();
            return response()->json(['message' => 'success!']);
        }

        $message->update(['deleted_by' => $user]);
        return response()->json(['message' => 'success']);

    }
}
