<?php

namespace App\Http\Resources;

use App\Http\Resources\Comment\CommentResource;
use App\Http\Resources\Post\UserDataResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use app\Http\Resources\Post;
use App\Http\Resources\Like\UserLikesResource;
use App\Http\Resources\Post\CommunityPostResource;
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
            'post_type' => $this->post_type,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->diffForHumans(),
            'user' => new UserDataResource($this->user),
            'community' => new CommunityPostResource($this->community),
//            'likes' => UserLikesResource::collection($this->likes),
            'likes_count' => $this->likes->count(),
            'comments' => CommentResource::collection($this->comments),
            'comments_count' => $this->comments->count(),
            'shares' => $this->shares,
        ];
    }
}
