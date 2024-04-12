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
        $user = auth()->id();
        $notifiable = $this->user_id === $user;
        $data = [
            'id' => $this->id,
            'subtopics' => $this->subtopics,
            'title' => $this->title,
            'content' => $this->content,
            'image' => $this->image,
            'video' => $this->video,
            'link' => $this->link,
            'post_type' => $this->post_type,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->diffForHumans(),
            'user' => [
                'id' => $this->user->id,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'middle_name' => $this->user->middle_name,
                'type' => $this->user->type,
                'user_name' => $this->user->user_name,
                'profile_picture' => $this->user->profile_picture,
            ],
//            'likes' => UserLikesResource::collection($this->likes),
//            'comments' => CommentResource::collection($this->comments),
            'likes_count'=> $this->likes_count,
            'comments_count'=> $this->comments_count,
            'shares' => $this->shares,
            'liked_by_user'=>$this->isLikedByUser($user),
        ];
        if ($notifiable) {
            $data['notifiable'] = $this->notifiable;
        }
        return $data;
    }

    private function isLikedByUser ($userId): bool
    {
        return $this->likes->contains('id', $userId);

    }
}
