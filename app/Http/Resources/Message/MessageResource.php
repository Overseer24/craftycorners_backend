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
        $messageData = [
            'id' => $this->id,
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

        // Check if the message has any attachments
        if ($this->has_attachment) {
            $attachments = $this->attachments->map(function ($attachment) {
                return [
                    'id' => $attachment->id,
                    'file_path' => $attachment->file_path,
                    'file_type' => $attachment->file_type,
                    'file_name' => $attachment->file_name,
                ];
            });

            $messageData['attachments'] = $attachments;
        }

        return $messageData;
    }
}
