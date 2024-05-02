<?php

namespace App\Http\Resources\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowSpecificReport extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $reportableArray = [];

        if ($this->reportable_type === 'App\Models\Post') {
            $reportableArray = [
                'id' => $this->reportable->id,
                'title' => $this->reportable->title,
                'content' => $this->reportable->content,
                'image' => $this->reportable->image,
                'video' => $this->reportable->video,
                'link' => $this->reportable->link,
                'post_type' => $this->reportable->post_type,
                'created_at' => $this->reportable->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->reportable->updated_at->format('Y-m-d H:i:s'),
            ];
        } elseif ($this->reportable_type === 'App\Models\Comment') {
            $reportableArray = [
                'id' => $this->reportable->id,
                'content' => $this->reportable->content,
                'created_at' => $this->reportable->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->reportable->updated_at->format('Y-m-d H:i:s'),
            ];
        } elseif ($this->reportable_type === 'App\Models\Conversation') {
            $reportableArray = [
                'id' => $this->reportable->id,
                'sender_id' => $this->reportable->sender_id,
                'receiver_id' => $this->reportable->receiver_id,
                'messages_count' => $this->reportable->messages_count,
                'created_at' => $this->reportable->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->reportable->updated_at->format('Y-m-d H:i:s'),
            ];
        }

       $data= [
            'id' => $this->id,
            'reason' => $this->reason,
            'description' => $this->description,
            'proof' => $this->proof,
            'is_resolved' => $this->is_resolved,
            'reportable' => $reportableArray,
            'reported_by' => [
                'id' => $this->user->id,
                'first_name' => $this->user->first_name,
                'middle_name' =>$this->user->middle_name,
                'last_name' => $this->user->last_name,
                'user_name' => $this->user->user_name,
                'program'=>$this->user->program,
                'student_id'=>$this->user->student_id,
            ],
            'reported_user'=>[
                'first_name' => $this->reportedUser->first_name,
                'middle_name' =>$this->reportedUser->middle_name,
                'last_name' => $this->reportedUser->last_name,
                'user_name' => $this->reportedUser->user_name,
                'program'=>$this->reportedUser->program,
                'student_id'=>$this->reportedUser->student_id,
            ],
        ];


        if ($this->is_resolved) {
            $data['resolution_option'] = $this->resolution_option;
            $data['resolution_description'] = $this->resolution_description;
        }

        return $data;

    }
}
