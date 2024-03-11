<?php

namespace App\Http\Resources\Message;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationsListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $auth_user = auth()->user();
//
//        $sender = ($this->sender_id === $auth_user)? 'auth_user' : 'receiver_id';
//        $receiver = ($this->receiver_id === $auth_user)? 'auth_user' : 'receiver_id';

        return [
            'id' => $this->id,

            'read' => $this->isRead(),
            'sender'=>[
                'sender_id' => $this->sender_id,
                'first_name' => $this->sender->first_name,
                'last_name' => $this->sender->last_name,
                'profile_picture' => $this->sender->profile_picture ?? 'default.jpg',
            ],
            'receiver'=>[
                'receiver_id' => $this->receiver_id,
                'first_name' => $this->receiver->first_name,
                'last_name' => $this->receiver->last_name,
                'profile_picture' => $this->receiver->profile_picture ?? 'default.jpg',
            ],
            'message' => ForConversationListResource::collection($this->messages)->last(),

            ];

    }
}
