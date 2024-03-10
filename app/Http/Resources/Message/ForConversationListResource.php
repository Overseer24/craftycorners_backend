<?php

namespace App\Http\Resources\Message;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ForConversationListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return[
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'sender' => $this->sender->first_name . ' ' . $this->sender->last_name,
            'message' => $this->message,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
