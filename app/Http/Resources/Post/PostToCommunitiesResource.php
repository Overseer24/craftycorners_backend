<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Resources\Json\JsonResource;

class PostToCommunitiesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
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
//            'likes' => UserLikesResource::collection($this->likes),
//            'comments' => CommentResource::collection($this->comments),
            'likes_count'=> $this->likes_count,
            'comments_count'=> $this->comments_count,
            'shares' => $this->shares,
            'liked_by_user'=>$this->isLikedByUser(auth()->id()),


        ];
    }

    private function isLikedByUser ($userId): bool
    {
        return $this->likes->contains('id', $userId);

    }
}
