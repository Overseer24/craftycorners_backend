<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Comment\CommentResource;
use App\Http\Resources\Like\UserLikesResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersPostResource extends JsonResource
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
            'title' => $this->title,
            'content' => $this->content,
            'image' => $this->image,
            'video' => $this->video,
            'link' => $this->link,
            'post_type' => $this->post_type,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->diffForHumans(),
            'likes' => UserLikesResource::collection($this->likes),
            'comments' => CommentResource::collection($this->comments),
            'shares' => $this->shares,
            'community'=>[
                'id' => $this->community->id,
                'name' => $this->community->name,
                'created_at' => $this->community->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->community->updated_at->diffForHumans(),
            ],
        ];
    }
}
