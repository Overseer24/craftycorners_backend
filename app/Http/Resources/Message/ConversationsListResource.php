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
        $latest_message = $this->messages->first();
        return [
            'id' => $this->id,
            'read' => $this->read,
            'receiver' => [
                'id' => $this->receiver->id,
                'first_name' => $this->receiver->first_name,
                'last_name' => $this->receiver->last_name,
            ],
            'latest_message' => [
                'id' => $latest_message ? $latest_message->id : '',
                'message' => $latest_message ? $latest_message->message : '',
                'created_at' => $latest_message ? $latest_message->created_at->format('Y-m-d H:i:s') : '',

            ]
        ];
    }
}
