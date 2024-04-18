<?php

namespace App\Http\Resources\Mentor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MentorsCommunityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray( $request): array
    {
        return[
            'id' => $this->id,
            'user_id' => $this->user_id,
            'specialization' => $this->specialization,
            'like_counts' => $this->like_counts,
            'user'=>[
                'id' => $this->user->id,
                'first_name' => $this->user->first_name,
                'middle_name' => $this->user->middle_name,
                'last_name' => $this->user->last_name,
                'profile_picture' => $this->user->profile_picture,
             ],

        ];
    }
}
