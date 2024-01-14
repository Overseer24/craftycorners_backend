<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\JoinedUserDetails\JoinedUserResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\Like\UserLikesResource as LikeResource;
use App\Http\Resources\Post\PostOnCommunitiesResource;

class CommunityResource extends JsonResource
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
            'name' => $this->name,
            'community_photo' => $this->community_photo,
            'cover_photo' => $this->cover_photo,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'members'=>JoinedUserResource::collection($this->joined),
            'posts'=>PostOnCommunitiesResource::collection($this->whenLoaded('posts')),
            // 'likes' => LikeResource::collection($this->whenLoaded('likes')),
        ];
    }
}
