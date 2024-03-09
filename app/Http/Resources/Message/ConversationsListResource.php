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
        $auth_user = auth()->id();
//
//        $sender = ($this->sender_id === $auth_user)? 'auth_user' : 'receiver_id';
//        $receiver = ($this->receiver_id === $auth_user)? 'auth_user' : 'receiver_id';

        return [
            'id' => $this->id,
            'user_id' => $auth_user,
            'read' => $this->isRead(),
            'message' => $this->messages,

            'sender'=>[
                'sender_id' => $this->sender_id,
                'first_name' => $this->sender->first_name,
                'last_name' => $this->sender->last_name,
            ],
            'receiver'=>[
                'receiver_id' => $this->receiver_id,
                'first_name' => $this->receiver->first_name,
                'last_name' => $this->receiver->last_name,
            ],
            ];

    }
}
