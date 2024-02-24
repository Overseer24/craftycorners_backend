<?php

namespace App\Http\Resources\Post;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecificUserPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserDataResource($this->user),
            'title' => $this->title,
            'content' => $this->content,
            'image' => $this->image,
            'video' => $this->video,
            'link' => $this->link,
            'post_type' => $this->post_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'community' => $this->community,
            'comments' => $this->comments,
            'likes_count'=> $this->likes->count(),
            'comments_count'=> $this->comments->count(),

            'shares' => $this->shares,
        ];
    }
}
