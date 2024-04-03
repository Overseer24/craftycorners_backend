<?php

namespace App\Http\Resources\Comment;

use Illuminate\Http\Resources\Json\JsonResource;


class CommentResource extends JsonResource
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
            'content' => $this->content,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->diffForHumans(),
            'poster' => $this->user->id,
            'user'=>[
                'id' => $this->user->id,
                'profile_picture' => $this->user->profile_picture,
                'user_name' => $this->user->user_name,
                'first_name' => $this->user->first_name,
                'type' => $this->user->type,

            ]
        ];
    }
}
