<?php

namespace App\Http\Resources\Mentor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthApprovedMentor extends JsonResource
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
            'specialization' => $this->specialization,
            'community' => [
                'id' => $this->community->id,
                'name' => $this->community->name,
            ],

        ];
    }
}
