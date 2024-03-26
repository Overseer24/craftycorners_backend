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
            'profile_picture' => $this->profile_picture,
            'type' => $this->type,
            'created_at' => $this->created_at->format('Y-m-d H:i:s')
        ];
    }
}
