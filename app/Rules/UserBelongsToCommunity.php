<?php

namespace App\Rules;

use App\Models\Community;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserBelongsToCommunity implements Rule
{
    private $communityId;

    public function __construct($communityId)
    {
        $this->communityId = $communityId;
    }

    public function passes($attribute, $value)
    {
        $user = Auth::user();
        $community = Community::find($this->communityId);
        return $user->communities->contains($community);
    }

    public function message()
    {
        return 'User must be a member of the community.';
    }
}
