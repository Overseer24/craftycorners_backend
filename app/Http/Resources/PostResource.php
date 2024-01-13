<?php

namespace App\Http\Resources;

use App\Http\Resources\Post\UserDataResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use app\Http\Resources\Post;
class PostResource extends JsonResource
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


            'user' => new UserDataResource($this->user),
            'community' => new CommunityResource($this->community),
            'comments' => CommentResource::collection($this->comments),
            'post_type' => $this->post_type, // 'post_type' => 'text', 'image', 'video', 'link

            'likes' => $this->likes,

            'shares' => $this->shares,
            // 'comments_count' => $this->comments_count, // Assuming you have a 'comments_count' column in your posts table
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->diffForHumans(),
        ];
    }
}
