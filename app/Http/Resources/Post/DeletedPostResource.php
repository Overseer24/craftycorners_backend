<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\Comment\CommentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeletedPostResource extends JsonResource
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
            'title' => $this->title,
            'content' => $this->content,
            'image' => $this->image,
            'video' => $this->video,
            'link' => $this->link,
            'post_type' => $this->post_type,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->diffForHumans(),
            'deleted_at' => $this->deleted_at->format('Y-m-d H:i:s'),
            'user' =>[
                'id' => $this->user->id,
                'first_name' => $this->user->first_name,
                'middle_name' =>$this->user->middle_name,
                'last_name' => $this->user->last_name,
                'user_name' => $this->user->user_name,
                'profile_picture' => $this->user->profile_picture,
                'type' => $this->user->type,
            ],
            'community_name'=>$this->community->name,
        ];
    }
}
