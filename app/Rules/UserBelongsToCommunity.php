<?php

namespace App\Rules;

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
        return Auth::user()->communities()->where('id', $this->communityId)->exists();
    }

    public function message()
    {
        return 'User must be a member of the community.';
    }
}
