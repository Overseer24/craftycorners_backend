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
        $user = auth()->id();
        $notifiable = $this->user_id === $user;
       $data = [
              'id'=>$this->id,
              'title'=>$this->title,
              'content'=>$this->content,
              'community_id'=>$this->community_id,
              'created_at'=>$this->created_at->format('Y-m-d H:i:s'),
              'updated_at'=>$this->updated_at->format('Y-m-d H:i:s'),
           'likes_count'=>$this->likes_count,
           'comments_count'=>$this->comments_count,
           'liked_by_user' => $this->isLikedByUser($user),
           'post_type' =>$this->post_type,
           'shares'=>$this->shares,
           'image'=>$this->image,
           'video'=>$this->video,
           'link'=>$this->link,
           'user' => [
               'id' => $this->user->id,
               'first_name' => $this->user->first_name,
               'middle_name' =>$this->user->middle_name,
               'last_name' => $this->user->last_name,
               'user_name' => $this->user->user_name,
               'profile_picture' => $this->user->profile_picture,
               'type' => $this->user->type,
           ],
              'community'=>[
                  'id'=>$this->community->id,
                  'name'=>$this->community->name,
              ],

       ];

         if($notifiable){
              $data['notifiable'] = $this->notifiable;

         }
        return $data;
    }

    private function isLikedByUser($userId): bool
    {
        return $this->likes->contains('id', $userId);
    }
}
