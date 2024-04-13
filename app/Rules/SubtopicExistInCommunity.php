<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Community;

class SubtopicExistInCommunity implements Rule
{
    protected $communityId;

    public function __construct($communityId)
    {
        $this->communityId = $communityId;
    }

    public function passes($attribute, $value)
    {
        // Retrieve the community
        $community = Community::find($this->communityId);

        // If the community doesn't exist or the subtopic isn't in the list of subtopics, return false
        if (!$community || !in_array($value, $community->subtopics)) {
            return false;
        }

        return true;
    }

    public function message()
    {
        return 'The selected subtopic does not exist in the community.';
    }
}
