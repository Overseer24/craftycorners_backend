<?php

// UserController.php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Post\UserPostsResource;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {

        $users = User::with('communities')->get();
        return UserResource::collection($users);
    }


    #/user
    public function me(Request $request)
    {
        $user = $request->user();

        $unreadMessagesCount = cache()->rememberForever('unreadMessagesCount-' . $user->id, function () use ($user) {
            return $user->unreadMessages()->count();
        }  );
        return response()->json([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'middle_name' => $user->middle_name,
            'last_name' => $user->last_name,
            'type' => $user->type,
            'birthday' => $user->birthday->format('Y-m-d'),
            'gender' => $user->gender,
            'profile_picture' => $user->profile_picture,
            'created_at' => $user->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $user->updated_at->format('Y-m-d H:i:s'),
            'unread_messages_count' => $unreadMessagesCount,
        ]);
    }


    //displaying profile
    public function show(User $user)
    {

        return new UserResource($user);
    }


    //fectch user posts

    public function showUserPost(User $user){


//
//        if($user->currentAccessToken()){
//            $personalAccessTokenId = $user->currentAccessToken()->id;
//            $personalAccessToken = Cache::remember('personal-access-token-'.$personalAccessTokenId, 60*60*24, function() use ($user){
//                return $user->currentAccessToken();
//            });
//        }
//        $postsCache = Cache::remember('user-posts-'.$user->id.'-'.request('page',1), 60*60*24, function() use ($user){
        $userPost =  $user->posts()->with('comments','likes','community')->orderBy('created_at', 'desc')->paginate(5);
//        });

        return UserPostsResource::collection($userPost);
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


