<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostLikeNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        //check if post is video,image or link


        return [
            'post'=>[
                'id' => $this->id,
                'user_id' => $this->user_id,
                'title' => $this->title,
                'description' => $this->content,
                'image' => $this->image,
                'video' => $this->video,
                'link' => $this->link,
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),

//                'title' => $this->post->title,
//                'description' => $this->post->description,
//                'created_at' => $this->post->created_at->format('Y-m-d H:i:s'),
                ],

            'liker' => $this->likes->reject(function ($user) {
                // Exclude the post owner from the list of likers
                return $user->id === $this->user_id;
            })->map(function ($user) {
                return [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'profile_picture' => $user->profile_picture,
                    'liked_at' => $this->updated_at->format('Y-m-d H:i:s'),
                ];
            })->toArray(),

//                'first_name' => $this->post_liker->first_name,
//                'last_name' => $this->post_liker->last_name,
//                'profile_picture' => $this->post_liker->profile_picture,



        ];
    }
}
