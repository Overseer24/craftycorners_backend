<?php

namespace App\Http\Resources\Message;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $auth_user = auth()->user();
        return [
            'id' => $this->id,
            'user_id' => $auth_user->id,
            'conversation_id' => $this->conversation_id,
            'message' => $this->message,
            'read' => $this->read,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'receiver'=>[
                'receiver_id' => $this->receiver_id,
                'first_name' => $this->receiver->first_name,
                'last_name' => $this->receiver->last_name,
                'profile_picture' => $this->receiver->profile_picture,
            ],

            ];
    }
}
