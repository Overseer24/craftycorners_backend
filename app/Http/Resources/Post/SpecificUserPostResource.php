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
            'user' => [
                'id' => $this->user->id,
                'first_name' => $this->user->first_name,
                'middle_name' =>$this->user->middle_name,
                'last_name' => $this->user->last_name,
                'user_name' => $this->user->user_name,
                'profile_picture' => $this->user->profile_picture,
                'type' => $this->user->type,
            ],
            'title' => $this->title,
            'content' => $this->content,
            'image' => $this->image,
            'video' => $this->video,
            'link' => $this->link,
            'post_type' => $this->post_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'liked_by_user'=>$this->isLikedByUser(auth()->id()),


            'community' => [
                'id' => $this->community->id,
                'name' => $this->community->name,
                'description' => $this->community->description,
                'image' => $this->community->image,
                'members_count' => $this->community->members_count,
            ],


            'likes_count'=> $this->likes_count,
            'comments_count'=> $this->comments_count,
            'shares' => $this->shares,
            'comments' => $this->comments,

        ];
    }

    private function isLikedByUser ($userId): bool
    {
        return $this->likes->contains('id', $userId);
    }
}
