<?php

namespace App\Http\Resources\User;
namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'profile_picture' => $this->profile_picture,
            'type' => $this->type,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'phone_number' => $this->phone_number,
            'gender' => $this->gender,
            'birthday' => $this->birthday->format('Y-m-d'),
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
//            'posts'=>UsersPostResource::collection($this->posts),
        ];
    }
}
