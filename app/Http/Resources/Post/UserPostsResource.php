<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPostsResource extends JsonResource
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
         'likes_count'=> $this->likes->count(),
         'comments_count'=> $this->comments->count(),
         'liked_by_user' => $this->isLikedByUser(auth()->id()),
         'community' => [
             'id' => $this->community->id,
             'name' => $this->community->name,
         ],
        'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        'updated_at' => $this->updated_at->diffForHumans(),

     ];
    }
    private function isLikedByUser($userId): bool
    {
        //cache the likes to rememberforever
        return $this->likes->contains('id', $userId);
    }
}
