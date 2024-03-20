<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\UserCommunitiesResource;
use App\Http\Resources\User\UsersPostResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'middle_name' =>$this->middle_name,
            'last_name' => $this->last_name,
            'user_name' => $this->user_name,
            'email' => $this->email,
            'birthday' => $this->birthday->format('Y-m-d'),
            'profile_picture' => $this->profile_picture,
            'type' => $this->type,
            'phone_number'=>$this->phone_number,
            'gender'=>$this->gender,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->diffForHumans(),
//            'assessment_completed'=>$this->pre_assessment_completed,
            'communities'=>$this->communities->map(function($community){
                return[
                    'id' => $community->id,
                    'name' => $community->name,
                    'community_photo' => $community->community_photo,
                    'description' => $community->description,
                    'created_at' => $community->created_at->format('Y-m-d H:i:s'),

                ];

            }),
     //   'unread_messages' => $this->unreadMessages(),
//            'posts'=>UsersPostResource::collection($this->posts),
        ];
    }
}
