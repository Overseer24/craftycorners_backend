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

        $results = collect();

        if(!$communityResult->isEmpty()){
            $results = $results->concat($communityResult->map(function ($community) {
                return [
                    'type' => 'community',
                    'id' => $community->id,
                    'name' => $community->name,
                    'description' => $community->description,
                ];
            }));
        }

        if (!$UserResult->isEmpty()) {
            $results = $results->concat($UserResult->map(function ($user) {
                return [
                    'type' => 'user',
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'middle_name' => $user->middle_name,
                    'last_name' => $user->last_name,
                    'user_name' => $user->user_name,
                    'profile_picture' => $user->profile_picture,
                ];
            }));
        }

        if (!$PostResult->isEmpty()) {
            $results = $results->concat($PostResult->map(function ($post) {
                return [
                    'type' => 'post',
                    'id' => $post->id,
                    'title' => $post->title,
                ];
            }));
        }

        $results = $results->slice(0, 5);

        $response = [
            'community'=>[],
            'user'=>[],
            'post'=>[],
        ];
        foreach ($results as $result) {
          array_push($response[$result['type']], $result);
        }


        if(empty($response['community']) && empty($response['user']) && empty($response['post'])){
            return response()->json(['message' => 'No result found'], 404);
        }
//        if($results->isEmpty()){
//            return response()->json(['message' => 'No result found'], 404);
//        }

        return response()->json($response);
    }

}
