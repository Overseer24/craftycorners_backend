<?php

namespace App\Http\Resources\JoinedUserDetails;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class JoinedUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return[
            'id' => $this->id,
            'profile_picture' => $this->profile_picture,
            'user_name' => $this->user_name,
            'type' => $this->type,
            'first_name' => $this->first_name,
            'middle_name' =>$this->middle_name,
            'last_name' => $this->last_name,
        ];
    }
}
