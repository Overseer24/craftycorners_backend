<?php

// UserController.php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\Post\UserPostsResource;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\UsersListResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function index()
    {

        $users = User::with('communities','experiences')->orderBy('created_at', 'desc')->paginate(10);
        return UsersListResource::collection($users);
    }

    #/user
    public function me(Request $request)
    {
        $user = $request->user();

        $unreadMessagesCount = cache()->rememberForever('unreadMessagesCount-' . $user->id, function () use ($user) {
            return $user->unreadMessages()->count();
        } );

        $unreadNotificationsCount = cache()->rememberForever('unreadNotificationsCount-' . $user->id, function () use ($user) {
            return $user->unreadNotifications()->count();
        } );

//        check if user is a mentor and show all approved mentor applications status

                return response()->json([
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'middle_name' => $user->middle_name,
                    'last_name' => $user->last_name,
                    'type' => $user->type,
                    'birthday' => $user->birthday->format('Y-m-d'),
                    'gender' => $user->gender,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'profile_picture' => $user->profile_picture,
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $user->updated_at->format('Y-m-d H:i:s'),
                    'unread_messages_count' => $unreadMessagesCount,
                    'assessment_completed'=>$user->pre_assessment_completed,
                ]);
    }


    public function getUserLevels(Request $request){
        $user = $request->user();
        $levelOnCommunity = [];
        foreach ($user->communities as $community) {
            $experience = $user->experiences->where('community_id', $community->id)->first();
            $levelOnCommunity[]=[
                'community_id'=>$community->id,
                'community_name'=>$community->name,
                'level'=>$experience ? $experience->level : null,
                'experience_points'=>$experience ? $experience->experience_points : null,
                'badge'=>$experience ? $experience->badge : null,
                'next_level_experience'=>$experience->next_experience_required ?? null,

            ];
        }
        return response()->json([
            'user_level'=>$levelOnCommunity,
        ]);
    }


    public function specificUserLevels(User $user)
    {


        $levelOnCommunity = [];
        foreach ($user->communities as $community) {
            // Get the experience for the current community
            $experience = $user->experiences->firstWhere('community_id', $community->id);
            $levelOnCommunity[] = [
                'community_id' => $community->id,
                'community_name' => $community->name,
                'level' => $experience ? $experience->level : null,
                'experience_points' => $experience ? $experience->experience_points : null,
                'badge' => $experience ? $experience->badge : null,
                'next_level_experience' => $experience->next_experience_required ?? null,
                ];
        }

        return response()->json([
            'user_level' => $levelOnCommunity,
        ]);
    }


    //displaying profile
    public function show(User $user)
    {

        return new UserResource($user);
    }
    //fectch user posts

    public function showUserPost(User $user){

        $userPost =  $user->posts()->with('comments','likes','community')->orderBy('created_at', 'desc')->paginate(5);

        return UserPostsResource::collection($userPost);
    }

    public function doneAssessment(){
        $user = auth()->user();
        $user->pre_assessment_completed = true;
        $user->save();
        return response()->json([
            'message' => 'Assessment status updated successfully'
        ]);
    }

    public function update(UserRequest $request, User $user)
    {
        // Validate the request
        $data = $request->validated();
        if ($request->hasFile('profile_picture')) {
            if($user->profile_picture) {
                Storage::delete('public/users/' . $user->profile_picture);
            }
            $file = $request->file('profile_picture');
            $fileName = $user->id . '.' . now()->format('YmdHis'). '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/users', $fileName);
            $data['profile_picture'] = $fileName;
        }
        $user->update($data);
        return new UserResource($user);
    }

    public function destroy(User $user)
    {
    if(auth()->user()->type != 'admin') {
        return response()->json([
        'message' => 'You are not authorized to delete this user'
        ], 403);
    }
        if($user->type == 'admin' ) {
            return response()->json([
                'message' => 'You cannot delete an admin'
            ], 403);
    }
    //permanently delete their account immediately
         $user->forceDelete();

    return response()->json([
        'message' => 'User deleted successfully'
    ]);
    }
    //add soft delete for admin to softban the accounts
//    public function softDelete(User $user)
//    {
//        if(auth()->user()->type != 'admin') {
//            return response()->json([
//            'message' => 'You are not authorized to soft delete this user'
//            ], 403);
//        }
//        if($user->type == 'admin' ) {
//            return response()->json([
//                'message' => 'You cannot soft delete an admin'
//            ], 403);
//        }
//        //soft delete their account
//        $user->delete();
//
//        return response()->json([
//            'message' => 'User soft deleted successfully'
//        ]);}
//
//
//    //lift soft ban
//    public function liftSoftBan(User $user)
//    {
//        if(auth()->user()->type != 'admin') {
//            return response()->json([
//            'message' => 'You are not authorized to lift soft ban on this user'
//            ], 403);
//        }
//        //lift soft ban
//        $user->restore();
//
//        return response()->json([
//            'message' => 'User soft ban lifted successfully'
//        ]);
//    }
   }

    // public function joinCommunity(Request $request, $communityId)
    // {
    //     $user = auth()->user();

    //     if($user->joinCommunity($communityId)){
    //         return response()->json([
    //             'message' => 'You have joined the community'
    //         ]);
    //     }

    //     else {
    //         return response()->json([
    //             'message' => 'You are already a member of this community'
    //         ], 400);
    //     }
    // }
    // public function joinCommunity(Request $request, $communityId)
    // {
    //     $user = auth()->user();

    //     if($user->joinCommunity($communityId)){
    //         return response()->json([
    //             'message' => 'You have joined the community'
    //         ]);
    //     }

    //     else {
    //         return response()->json([
    //             'message' => 'You are already a member of this community'
    //         ], 400);
    //     }
    // }


