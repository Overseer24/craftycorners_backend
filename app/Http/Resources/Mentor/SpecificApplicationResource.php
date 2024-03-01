<?php

namespace App\Http\Resources\Mentor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecificApplicationResource extends JsonResource
{


    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
          'id' => $this->id,
            'student_id' => $this->student_id,
            'program' => $this->Program,
            'community' => [
                'id' => $this->community->id,
                'name' => $this->community->name,
                'community_photo' => $this->community->community_photo,
            ],
            'user'=>[
                'id' => $this->user->id,
                'user_name' => $this->user->user_name,
                'first_name' => $this->user->first_name,
                'middle_name' =>$this->user->middle_name,
                'last_name' => $this->user->last_name,
                'email' => $this->user->email,
                'profile_picture' => $this->user->profile_picture,
               ],
            'date_of_assessment'=> $this->date_of_Assessment ? $this->date_of_Assessment->format('Y-m-d H:i:s') : null,
            'specialization' => $this->specialization,
            'status' => $this->status,
        ];
    }
}
