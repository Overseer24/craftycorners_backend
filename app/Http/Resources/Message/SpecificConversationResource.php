<?php

namespace App\Http\Resources\Message;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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

        return[
            'id' => $this->id,
            'sender' => [
                'id' => $this->sender->id,
                'first_name' => $this->sender->first_name,
                'last_name' => $this->sender->last_name,
            ],
            'receiver' => [
                'id' => $this->receiver->id,
                'first_name' => $this->receiver->first_name,
                'last_name' => $this->receiver->last_name,
                'profile_picture' => $this->receiver->profile_picture,
            ],
           'messages' => ForSpecificMessageResource::collection($this->messages)
        ];
    }
}
