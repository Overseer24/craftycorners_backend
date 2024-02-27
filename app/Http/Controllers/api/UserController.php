<?php

// UserController.php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Post\UserListResource;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
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

    public function show(User $user)
    {

        return new UserResource($user);
    }


    //fectch user posts

    public function showUserPost(User $user){
        $postsCache = Cache::remember('user-posts-'.request('page',1), 60*60*24, function() use ($user){
            return $user->posts()->with('user','comments','likes','community')->orderBy('created_at', 'desc')->paginate(5);
        });

//        $postsCache->load();
        return UserListResource::collection($postsCache);
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
    $user->delete();
    return response()->json([
        'message' => 'User deleted successfully'
    ]);
    }
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


