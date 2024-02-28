<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommunityResource;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Community;
use App\Http\Requests\UserCommunityRequest;
use App\Http\Requests\JoinCommunityRequest;
use App\Http\Requests\LeaveCommunityRequest;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;


class UserCommunityController extends Controller
{
    public function joinCommunity(Community $community)
    {
        $join = auth()->user();
        if ($join->communities->contains($community)) {
            return response()->json([
                'message' => 'User is already a member of this community',
            ], 400);
        }
        Artisan::call('cache:clear');
        $join->communities()->attach($community);
        return response()->json([
            'message' => 'User has joined the community',
        ]);
    }

    public function leaveCommunity(Community $community)
    {
        $user = auth()->user();
        if (!$user->communities->contains($community)) {
            return response()->json([
                'message' => 'User is not a member of this community',
            ], 400);
        }


//
//        $remainingCommunityIds = $user->communities()->pluck('community_id')->toArray();
//        $maxPage = ceil(Post::whereIn('community_id', $remainingCommunityIds)->count() / 5); // Assuming 5 posts per page
//        for ($page = 1; $page <= $maxPage; $page++) {
//            $cacheKey = 'homepage-posts-' . $user->id . '-' . $page;
//            Cache::forget($cacheKey);
//        }
        $user->communities()->detach($community);


        // Clear cache for each page
//        for ($page = 1; $page <= 100; $page++) {
//            $cacheKey = 'homepage-posts-' . $user->id . '-' . $page;
//            Cache::forget($cacheKey);
//        }

        Artisan::call('cache:clear');
        return response()->json([
            'message' => 'User has left the community',
        ]);

    }

    public function showCommunityMembers($communityid)
    {
        $community = Community::find($communityid);
        $user = $community->joined;
        return response()->json([
            'message' => 'Community members retrieved successfully',
            'community' => $community->name,
            'members' => $user
        ]);
    }
}
