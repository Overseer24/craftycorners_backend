<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $communityResult = Community::search($search)->get();

        $UserResult = User::search($search)->get();

        $PostResult = Post::search($search)->get();

//        return response()->json([
//            'community' => $communityResult->map(function ($community) {
//                return [
//                    'id' => $community->id,
//                    'name' => $community->name,
//                    'description' => $community->description,
//                ];
//            }),
//
//            'user' => $UserResult->map(function ($user) {
//                return [
//                    'id' => $user->id,
//                    'first_name' => $user->first_name,
//                    'middle_name' => $user->middle_name,
//                    'last_name' => $user->last_name,
//                    'user_name' => $user->user_name,
//                    'profile_picture' => $user->profile_picture,
//                    'type' => $user->type,
//        ];
//            })
//        ]);
        $response=[];
        if(!$communityResult->isEmpty()){
            $response['community'] = $communityResult->map(function ($community) {
                return [
                    'id' => $community->id,
                    'name' => $community->name,
                    'description' => $community->description,
                ];
            });
        }

        if (!$UserResult->isEmpty()) {
            $response['user'] = $UserResult->map(function ($user) {
                return [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'middle_name' => $user->middle_name,
                    'last_name' => $user->last_name,
                    'user_name' => $user->user_name,
                    'profile_picture' => $user->profile_picture,
                    'type' => $user->type,
                ];
            });
        }


        if (!$PostResult->isEmpty()) {
            $response['post'] = $PostResult->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                ];
            });
        }


        if(empty($response)){
            return response()->json(['message' => 'No result found'], 404);
        }

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
