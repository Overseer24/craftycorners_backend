<?php

namespace App\Http\Resources\Community;


use Illuminate\Http\Resources\Json\JsonResource;


class CommunityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $user = auth()->user();
        // Check if user is a mentor and approved in the said community
        $is_mentor = $user && $this->mentor()->where('user_id', $user->id)->where('status', 'approved')->exists();
        $is_user_member = $user && $this->joined->contains('id', $user->id);

        // Fetch the user's experiences for the current community
        $user_experiences = $user->experiences->where('community_id', $this->id)->first();
        $user_level = $user_experiences ? $user_experiences->level : null;
        $user_experience_points = $user_experiences ? $user_experiences->experience_points : null;
        $user_badge = $user_experiences ? $user_experiences->badge : null;
        $next_level_experience = $user_experiences ? $user_experiences->next_experience_required : null;

        // Initialize the array with basic community information
        $array = [
            'is_user_member' => $is_user_member,
            'id' => $this->id,
            'name' => $this->name,
            'community_photo' => $this->community_photo,
            'cover_photo' => $this->cover_photo,
            'description' => $this->description,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'members_count' => $this->members_count,
            'members' => $this->joined->map(function($joined) {
                return [
                    'id' => $joined->id,
                    'first_name' => $joined->first_name,
                    'middle_name' => $joined->middle_name,
                    'last_name' => $joined->last_name,
                    'profile_photo' => $joined->profile_photo,
                    'type' => $joined->type,
                ];
            }),
        ];

        // Include additional information for mentors
        if ($is_mentor) {
            $array['is_user_mentor'] = true;
        }

        // Include additional information for members
        if ($is_user_member) {
            $array['user_level'] = $user_level;
            $array['user_experience_points'] = $user_experience_points;
            $array['badge'] = $user_badge;
            $array['next_level_experience'] = $next_level_experience;
        }

        return $array;
    }

}
