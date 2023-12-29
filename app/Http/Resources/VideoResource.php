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
            'author' => $this->author,
            'community_id' => $this->community_id,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->diffForHumans(),
        ];
    }
}
