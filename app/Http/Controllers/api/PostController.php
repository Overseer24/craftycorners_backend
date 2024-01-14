<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Resources\PostResource;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Str;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Requests\Post\StorePostRequest;
use Response;
use Illuminate\Support\Facades\Storage;


class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with('community', 'user')->get();
        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function show(Post $post)
    {
        return new PostResource($post);
    }
    public function store(StorePostRequest $request)
    {
        $user = auth()->user()->posts()->create($request->validated());

        if($request->hasFile('video')){
            $file = $request->file('video');
            $fileName = $user->id . '.' . now()->format('YmdHis'). '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/posts', $fileName);
            $user->video = $fileName;
            $user->save();
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = $user->id . '.' . now()->format('YmdHis'). '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/posts', $fileName);
            $user->image = $fileName;
            $user->save();
        }
        return new PostResource($user);

    }

    public function update(UpdatePostRequest $request, Post $post)
    {

        // Validate the request
        $validatedData = $request->validated();

        // Move the file if present in the request
        if($request->hasFile('video')){
            if($post->video) {
                Storage::delete('public/posts/' . $post->video);
            }
            $file = $request->file('video');
            $fileName = $post->id . '.' . now()->format('YmdHis'). '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/posts', $fileName); // Use Laravel storage for file storage
            $post->video = $fileName;
        }

        if ($request->hasFile('image')) {
            if($post->profile_picture) {
                Storage::delete('public/posts/' . $post->profile_picture);
            }
            $file = $request->file('image');
            $fileName = $post->id . '.' . now()->format('YmdHis'). '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/posts', $fileName); // Use Laravel storage for file storage
            $post->image = $fileName;
        }

        // Update other attributes
        $post->update($validatedData);

        return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json([
            'message' => 'Post deleted successfully'
        ]);
    }


    public function like (Post $post){
        $liker = auth()->user();
        if($liker->likes()->where('post_id', $post->id)->exists()){
            return response()->json([
                'message' => 'Post already liked'
            ]);
        }

        $liker->likes()->attach($post);
        return response()->json([
            'message' => 'Post liked successfully'
        ]);
    }

    public function unlike (Post $post){
        $post->likes()->detach(auth()->user()->id);
        return response()->json([
            'message' => 'Post unliked successfully'
        ]);
    }

}
