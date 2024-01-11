<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommunityResource;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Community;
use App\Http\Requests\UserCommunityRequest;
use App\Http\Requests\JoinCommunityRequest;
use App\Http\Requests\LeaveCommunityRequest;

class UserCommunityController extends Controller
{
    public function joinCommunity(JoinCommunityRequest $request ){
        $user = User::find($request->user_id);
        $community = Community::find($request->community_id);

        if(!$user->communities->contains($community)){
            $user->communities()->attach($community);
            return response()->json([
                'message' => 'User has joined the community',
            ]);
        }
        else{
            return response()->json([
                'message' => 'User is already a member of this community',
            ], 400);
        }
    }

    public function leaveCommunity(LeaveCommunityRequest $request){
        $user = User::find($request->user_id);
        $community = Community::find($request->community_id);

        if($user->communities->contains($community)){
            $user->communities()->detach($community);
            return response()->json([
                'message' => 'User has left the community',
            ]);
        }
        else{
            return response()->json([
                'message' => 'User is not a member of this community',
            ], 400);
        }
    }

    public function showCommunityMembers($communityid) {
        $community = Community::find($communityid);
        $user = $community->joined;
        return response()->json([
            'message' => 'Community members retrieved successfully',
            'community' => $community->name,
            'members' => $user


        ]);
    }
}
