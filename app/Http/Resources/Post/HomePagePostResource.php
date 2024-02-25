<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomePagePostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
       return[
              'id'=>$this->id,
              'title'=>$this->title,
              'content'=>$this->content,
              'community_id'=>$this->community_id,
              'created_at'=>$this->created_at->format('Y-m-d H:i:s'),
              'updated_at'=>$this->updated_at->format('Y-m-d H:i:s'),
           'likes_count'=>$this->likes->count(),
           'comments_count'=>$this->comments->count(),
           'liked_by_user'=>$this->isLikedByUser(auth()->id()),
           'post_type' =>$this->post_type,
           'shares'=>$this->shares,
           'image'=>$this->image,
           'video'=>$this->video,
           'link'=>$this->link,
           'user' => new UserDataResource($this->user),
              'community'=>$this->community,

       ];
    }

    private function isLikedByUser ($userId): bool
    {
        return $this->likes->contains('user_id', $userId);
    }
}
