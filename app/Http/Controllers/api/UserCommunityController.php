<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Community;
use Illuminate\Support\Facades\DB;


class UserCommunityController extends Controller
{
    public function joinCommunity(Community $community)
    {
        $user = auth()->user();
        if ($user->communities->contains($community)) {
            return response()->json([
                'message' => 'User is already a member of this community',
            ], 400);
        }

        $user->communities()->attach($community);
        $community->updateMembersCount();

        // Create the user's experience record for the community if it doesn't exist
        $initialLevel = 1;
        $badge = DB::table('levels')->where('level', $initialLevel)->value('badge');
        $user->experiences()->firstOrCreate([
            'community_id' => $community->id,
        ], [
            'experience_points' => 0,
            'level' => $initialLevel,
            'badge' => $badge,
        ]);


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
        $community->updateMembersCount();

        // Clear cache for each page
//        for ($page = 1; $page <= 100; $page++) {
//            $cacheKey = 'homepage-posts-' . $user->id . '-' . $page;
//            Cache::forget($cacheKey);
//        }

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
