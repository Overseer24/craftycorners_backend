<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostShareNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return[
            'post'=>[
                'id' => $this->id,
                'user_id' => $this->user_id,
                'title' => $this->title,
                'description' => $this->content,
                'image' => $this->image,
                'video' => $this->video,
                'link' => $this->link,
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            ],
            'sharer' => $this->shares->reject(function ($user) {
                // Exclude the post owner from the list of sharers
                return $user->id === $this->user_id;
            })->map(function ($user) {
                return [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'profile_picture' => $user->profile_picture,
                    'shared_at' => $this->updated_at->format('Y-m-d H:i:s'),
                ];
            })->toArray(),
            'community' => [
                'id' => $this->community->id,
                'name' => $this->community->name,
            ],
        ];
    }
}
