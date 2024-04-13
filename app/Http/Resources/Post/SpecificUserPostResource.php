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

        $userId = auth()->id();
        //add 'notifiable' section if the user who view the post is the owner of the post
        $notifiable = $this->user_id === $userId;


        $data= [

            'liked_by_user'=>$this->isLikedByUser($userId),
            'id' => $this->id,
            'subtopics' => $this->subtopics,
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
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),

            'community' => [
                'id' => $this->community->id,
                'name' => $this->community->name,
                'description' => $this->community->description,
                'image' => $this->community->image,
                'members_count' => $this->community->members_count,
                'is_user_member'=> $this->isAuthUserMemberOfCommunity($userId),
            ],
            'likes_count'=> $this->likes_count,
            'comments_count'=> $this->comments_count,
            'shares' => $this->shares,


        ];
        if($notifiable){
            $data['notifiable'] = $this->notifiable;
            }

        return $data;

    }

    private function isLikedByUser ($userId): bool
    {
        return $this->likes->contains('id', $userId);
    }

    private function isAuthUserMemberOfCommunity($userId): bool
    {

        return $this->community->joined->contains('id', $userId);

    }
}
