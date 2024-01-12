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

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with(['community', 'user'])->get();
        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function show(Post $post)
    {
        $post->load('comments');
        return new PostResource($post);
    }
    public function store(UpdatePostRequest $request)
    {
        $user = auth()->user()->posts()->create($request->validated());
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
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
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension(); // Generate a UUID as the file name
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
}
