<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportedPost extends JsonResource
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
            'post_id' => $this->post->id,
            'title' => $this->post->title,
            'content' => $this->post->content,
            'image' => $this->post->image,
            'video' => $this->post->video,
            'post_type' => $this->post->post_type,
            'created_at' => $this->post->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->post->updated_at->format('Y-m-d H:i:s'),
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
    }
}
