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
        //check if user is mentor and is approve in the said community
        $is_mentor =$user && $this->mentor()->where('user_id', $user->id)->where('status', 'approved')->exists();
        $is_user_member = $user && $this->joined->contains('id', $user->id);
        $array=[
            'is_user_member'=>$is_user_member,
            'id' => $this->id,
            'name' => $this->name,
            'community_photo' => $this->community_photo,
            'cover_photo' => $this->cover_photo,
            'description' => $this->description,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'members_count'=>$this->members_count,
            'members'=>$this->joined->map(function($joined){
                return [
                    'id'=>$joined->id,
                    'first_name'=>$joined->first_name,
                    'middle_name'=>$joined->middle_name,
                    'last_name'=>$joined->last_name,
                    'profile_photo'=>$joined->profile_photo,
                    'type'=>$joined->type,
                    'level'=>$joined->getLevel($this->id),
                    'experience_points'=>$joined->experiences()->where('community_id', $this->id)->value('experience_points'),
//                    'created_at'=>$joined->created_at->format('Y-m-d H:i:s'),
                ];
            }),

        ];
        if ($is_mentor){
            $array['is_user_mentor'] = true;
        }
        if($is_user_member){
            $array['user_level'] = $user->getLevel($this->id);
            $array['user_experience_points'] = $user->experiences()->where('community_id', $this->id)->value('experience_points');
            $array['badge'] = $user->experiences()->where('community_id', $this->id)->value('badge');
            $array['next_level_experience'] = $user->nextLevelExperience($this->id);


        }

        return $array;
    }
}
