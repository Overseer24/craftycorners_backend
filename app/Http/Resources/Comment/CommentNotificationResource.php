<?php

namespace App\Http\Resources\Comment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->diffForHumans(),
            'post_id' => $this->post_id,
            'poster'=>$this->post->user->id,
            'commenter'=>[
                'id' => $this->user->id,
                'profile_picture' => $this->user->profile_picture,
                'user_name' => $this->user->user_name,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'type' => $this->user->type,
            ],
            'community'=>[
                'id' => $this->post->community->id,
                'name' => $this->post->community->name,
            ],
        ];
    }
}
