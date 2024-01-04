<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
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
            'video_title' => $this->video_title,
            'video_description' => $this->video_description,
            'video_url' => $this->video_url,
            'video_photo' => $this->video_photo,
            'creator' => $this->creator,
            'created_at' => $this->created_at->format('d-m-Y'),
            'updated_at' => $this->updated_at->diffForHumans(),
            'community'=>[
                'id' => $this->community->id,
                'name' => $this->community->name,
            ],
            'user'=>[
                'id' => $this->user->id,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
            ]
        ];
    }
}
