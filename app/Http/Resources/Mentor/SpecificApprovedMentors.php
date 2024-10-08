<?php

namespace App\Http\Resources\Mentor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecificApprovedMentors extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {

        $user= auth()->user();
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'program' => $this->Program,
            'like_counts' => $this->like_counts,
            'community' => [
                'id' => $this->community->id,
                'name'=>$this->community->name,
            ],
            'mentor'=>[
                'user_id' => $this->user->id,
                'user_name' => $this->user->user_name,
                'first_name' => $this->user->first_name,
                'middle_name' =>$this->user->middle_name,
                'last_name' => $this->user->last_name,
                'email' => $this->user->email,
                'profile_picture' => $this->user->profile_picture,
            ],
            'specialization' => $this->specialization,
           'liked_by_user' => $user->hasLikedMentor($this->id)
        ];
    }
}
