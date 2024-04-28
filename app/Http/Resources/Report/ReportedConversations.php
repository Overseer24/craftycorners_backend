<?php

namespace App\Http\Resources\Report;

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
            'id'=>$this->id,
            'reason'=>$this->reason,
            'description'=>$this->description,
            'is_resolved'=>$this->is_resolved,
            'proof'=>$this->proof,
            'conversation' => $this->reportable?[
                'id' => $this->reportable->id,
                'sender_id' => $this->reportable->sender_id,
                'receiver_id' => $this->reportable->receiver_id,
                'messages_count' => $this->reportable->messages_count,
                'created_at' => $this->reportable->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->reportable->updated_at->format('Y-m-d H:i:s'),
            ]:null,
            'reported_by' =>$this->user? [
                'id' => $this->user->id,
                'first_name' => $this->user->first_name,
                'middle_name' =>$this->user->middle_name,
                'last_name' => $this->user->last_name,
                'user_name' => $this->user->user_name,
                'program'=>$this->user->program,
                'student_id'=>$this->user->student_id,
            ]:null,
            'reported_user'=>$this->reportedUser?[
                'first_name' => $this->reportedUser->first_name,
                'middle_name' =>$this->reportedUser->middle_name,
                'last_name' => $this->reportedUser->last_name,
                'user_name' => $this->reportedUser->user_name,
                'program'=>$this->reportedUser->program,
                'student_id'=>$this->reportedUser->student_id,
            ]:null,
        ];
    }
}
