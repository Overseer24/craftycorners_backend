<?php

namespace App\Http\Resources\Message;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportedConversations extends JsonResource
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
            'reason' => $this->reason,
            'description' => $this->description,
            'is_resolved' => $this->is_resolved,
            'user' => [
                'id' => $this->user->id,
                'first_name' => $this->user->first_name,
                'middle_name' =>$this->user->middle_name,
                'last_name' => $this->user->last_name,
                'user_name' => $this->user->user_name,
                'profile_picture' => $this->user->profile_picture,
                'type' => $this->user->type,
                'program'=>$this->user->program,
                'student_id'=>$this->user->student_id,
            ],
            'conversation'=>[
                'id' => $this->conversation->id,
                'created_at' => $this->conversation->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->conversation->updated_at->format('Y-m-d H:i:s'),
            ]

        ];
    }
}
