<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Post\HomePagePostResource;
use App\Http\Resources\Post\SpecificUserPostResource;
use FFMpeg\Format\Video\X264;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Resources\PostResource;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Str;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Requests\Post\StorePostRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Community;
use App\Http\Resources\Post\PostToCommunitiesResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

// use ProtoneMedia\LaravelFFMpeg\Support\ServiceProvider as FFMpegServiceProvider;
// use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
// use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    //show all post
    public function index()
    {
        $postCache = Cache::remember('posts-page-'.request('page',1), 60*60, function(){
            return Post::with('community', 'user')->orderBy('created_at', 'desc')->paginate(5);
        });
        return PostResource::collection($postCache);


   }

    /**
     * Store a newly created resource in storage.
     */

    //show all post of the users homepage base on the community they joined to
    public function showHomepagePost()
    {
      $user = auth()->user();

      $joinedCommunityId = $user->communities()->pluck('community_id')->toArray();

      $postCache=Cache::remember('homepage-posts-'.request('page',1), 60, function() use ($joinedCommunityId){
          return Post::with('user')
              ->whereIn('community_id', $joinedCommunityId)
              ->orderBy('created_at', 'desc')
              ->paginate(5);
      });

      return HomePagePostResource::collection($postCache);

    }
    public function show(Post $post)
    {
        return new SpecificUserPostResource($post);
    }


    //show all the post in the community
    public function showPostByCommunity(Community $communityId)
    {
       $cacheKey = 'community_posts_'.$communityId->id;
       $post = Cache::remember($cacheKey.request('page',1), 60, function() use ($communityId){
           return $communityId->posts()->with('user')->orderBy('created_at', 'desc')->paginate(5);
       });
        return PostToCommunitiesResource::collection($post);

    }

    public function store(StorePostRequest $request)
    {

        $user = auth()->user()->posts()->create($request->validated());
        /*reminder: if client side has problem with differentiating between video and image,
         add logic if video is present, then image is not present and vice versa*/

        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $fileName = $user->id . '.' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/posts', $fileName);
//
//
//            $lowFormat = (new X264('aac'))->setKiloBitrate(500);
//            $highFormat = (new X264('aac'))->setKiloBitrate(1000);
//            FFMpeg::fromDisk('public')
//                ->open('posts/' . $fileName)
//                ->exportForHLS()
////                ->toDisk('public')
//                ->addFormat($lowFormat, function ($filters) {
//                    $filters->resize(1280, 720);
//                })
//                ->addFormat($highFormat, function ($filters) {
//                    $filters->resize(1920, 1080);
//                })
//
//                ->save('posts/'. $fileName . '.m3u8');

            $user->video = $fileName;
            $user->save();
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = $user->id . '.' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/posts', $fileName);
            $user->image = $fileName;
            $user->save();
        }

        return new PostResource($user);
    }

    //Create Post on the community

//    public function postInCommunity(Community $community, StorePostRequest $request)
//    {
//
////        check if user is in the community
//        if (!auth()->user()->communities()->where('community_id', $community->id)->exists()) {
//            return response()->json([
//                'message' => 'You are not a member of this community'
//            ], 403);
//        }
//       $validatedData = $request->validated();
//         $validatedData['community_id'] = $community->id;
//        $user = auth()->user()->posts()->create($validatedData);
//
//
//        if (request()->hasFile('video')) {
//            $file = request()->file('video');
//            $fileName = $user->id . '.' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
//            $file->storeAs('public/posts', $fileName);
//            $user->video = $fileName;
//            $user->save();
//        }
//        if (request()->hasFile('image')) {
//            $file = request()->file('image');
//            $fileName = $user->id . '.' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
//            $file->storeAs('public/posts', $fileName);
//            $user->image = $fileName;
//            $user->save();
//        }
//
//        return response()->json([
//            'message' => 'Post created successfully'
//        ]);
//
//    }




    public function update(UpdatePostRequest $request, Post $post)
    {

        // Validate the request
        $validatedData = $request->validated();

        /*reminder: if client side has problem with differentiating between video and image,
         add logic if video is present, then image is not present and vice versa*/


        // Move the file if present in the request
        if ($request->hasFile('video')) {
            if ($post->video) {
                Storage::delete('public/posts/' . $post->video);
            }
            $file = $request->file('video');
            $fileName = $post->id . '.' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/posts', $fileName); // Use Laravel storage for file storage
            $post->video = $fileName;
        }

        if ($request->hasFile('image')) {
            if ($post->profile_picture) {
                Storage::delete('public/posts/' . $post->profile_picture);
            }
            $file = $request->file('image');
            $fileName = $post->id . '.' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
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


    public function like(Post $post)
    {
        $liker = auth()->user();
        if ($liker->likes()->where('post_id', $post->id)->exists()) {
            return response()->json([
                'message' => 'Post already liked'
            ]);
        }

        $liker->likes()->attach($post);
        return response()->json([
            'message' => 'Post liked successfully'
        ]);
    }

    public function unlike(Post $post)
    {
        $post->likes()->detach(auth()->user()->id);
        return response()->json([
            'message' => 'Post unliked successfully'
        ]);
    }

}
