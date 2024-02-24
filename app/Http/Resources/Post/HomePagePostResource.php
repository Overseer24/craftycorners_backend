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
              'user_id'=>$this->user_id,
              'created_at'=>$this->created_at->format('Y-m-d H:i:s'),
              'updated_at'=>$this->updated_at->format('Y-m-d H:i:s'),
           'user' => new UserDataResource($this->user),
              'community'=>$this->community,
           'likes_count'=>$this->likes->count(),
           'comments_count'=>$this->comments->count(),
       ];
    }
}
