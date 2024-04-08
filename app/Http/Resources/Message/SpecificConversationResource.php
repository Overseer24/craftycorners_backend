<?php

namespace App\Http\Resources\Message;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class SpecificConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        //get all messages
        $messages =[];

        foreach($this->messages as $message){
            $messages[] = [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'message' => $message->message,
                'read' => $message->read,
                'created_at' => $message->created_at->format('Y-m-d H:i:s'),
            ];
        }
        return[
            'id' => $this->id,
            'user_0' => [
                'id' => $this->sender->id,
                'first_name' => $this->sender->first_name,
                'last_name' => $this->sender->last_name,
                'profile_picture' => $this->sender->profile_picture,
            ],
            'user_1' => [
                'id' => $this->receiver->id,
                'first_name' => $this->receiver->first_name,
                'last_name' => $this->receiver->last_name,
                'profile_picture' => $this->receiver->profile_picture,
            ],
           'messages' => $messages,

            'meta'=>[
                'current_page' => $this->messages->currentPage(),
               'last_page' => $this->messages->lastPage(),
                'total_items' => $this->messages->total(),

            ]

        ];
    }
}
