<?php

namespace App\Http\Controllers\api;

use App\Events\PostInteraction;
use App\Events\PostLike;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\Post\DeletedPostResource;
use App\Http\Resources\Post\HomePagePostResource;
use App\Http\Resources\Post\PostLikeNotificationResource;
use App\Http\Resources\Post\PostResource;
use App\Http\Resources\Post\PostShareNotificationResource;
use App\Http\Resources\Post\PostToCommunitiesResource;
use App\Http\Resources\Post\SpecificUserPostResource;
use App\Jobs\CheckImageContent;
use App\Jobs\ProcessImage;
use App\Models\Community;
use App\Models\Post;
use App\Notifications\PostLiked;
use App\Notifications\PostShared;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

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
//        $posts= Post::with('user','lies')->orderBy('created_at', 'desc')->paginate(5);

//        $postCache = Cache::remember('posts-page-'.request('page',1), 60*60, function(){
//            return
        $posts = Post::with('community','user','comments.user','likes')->orderBy('created_at', 'desc')->paginate(5);
//        });
        return PostResource::collection($posts);
   }

    //show all post of the users homepage base on the community they joined to
    public function showHomepagePost()
    {
      $user = auth()->user();

      $joinedCommunityId = $user->communities()->pluck('community_id')->toArray();

//      $postCache=Cache::remember('homepage-posts-'.$user->id.'-'.request('page',1), 60*60, function() use ($joinedCommunityId){
//          return
        $homePagepost =  Post::with(['user','community','comments','likes'])
            ->whereHas('user', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->whereIn('community_id', $joinedCommunityId)
            ->orderBy('created_at', 'desc')
            ->paginate(5);
//      });
      return HomePagePostResource::collection($homePagepost);
    }
    //show the post of a specific user that is authenticated


    public function show(Post $post)
    {
        $post->load('user');
        return response()->json([
            'data' => new SpecificUserPostResource($post),
        ]);
    }

    //show all the post in the community
    public function showPostByCommunity(Community $community)
    {
//       $post = Cache::remember('community-posts-'.$community->id.'-'.request('page',1), 60*60*24, function() use ($community){
//
           $post = $community->posts()->with(['user','comments','likes'])
               ->whereHas('user', function ($query) {
                   $query->whereNull('deleted_at');
               })
               ->orderBy('created_at', 'desc')
               ->paginate(5);
//       });

        return PostToCommunitiesResource::collection($post);

    }

    public function showPostBySubtopic(Request $request, Community $community)
    {
        $subtopic = $request->input('subtopic');
        if(!$subtopic){
            return response()->json([
                'message' => 'Subtopic is required'
            ], 400);
        }
        $post = $community->posts()->where('subtopics', 'like', '%' . $subtopic . '%')
            ->with('user','likes')
            ->whereHas('user', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->orderBy('created_at', 'desc')->paginate(5 );

        if ($post->isEmpty()){
            return response()->json([
                'message' => 'No post found'
            ], 404);
        }
        return PostToCommunitiesResource::collection($post);

    }

    public function store(StorePostRequest $request)
    {
        $validatedData = $request->validated();
        $user = auth()->user();
        $validatedData['notifiable'] = $request->input('notifiable') === 'true';
        $post = $user->posts()->create($validatedData);
        /*reminder: if client side has problem with differentiating between video and image,
         add logic if video is present, then image is not present and vice versa*/

        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $fileName = $post->id . '.' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/posts', $fileName);

//
//
//            $lowFormat = (new X264('aac'))->setKiloBitrate(500);
//            $highFormat = (new X264('aac'))->setKiloBitrate(1000);
//            FFMpeg::fromDisk('public')
//                ->open('posts/' . $fileName)
//                ->exportForHLS()
//                ->toDisk('public')
//                ->addFormat($lowFormat, function ($filters) {
//                    $filters->resize(1280, 720);
//                })
//                ->addFormat($highFormat, function ($filters) {
//                    $filters->resize(1920, 1080);
//                })
//
//                ->save('posts/'. $fileName . '.m3u8');

            $post->video = $fileName;
            $post->save();
        }
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = $post->id . '.' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/posts', $fileName);
            $post->image = $fileName;
            $post->save();
            //dispatch a job to check image content

            CheckImageContent::dispatch($post);

        }


        //add experience
        $user->addExperiencePoints(25, $post->community_id);
        return response()->json([
            'message' => 'Post created successfully',
        ]);
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
        //ensure that the user is the owner of the post
        if (auth()->user()->id !== $post->user_id){
            return response()->json([
                'message' => 'You are not the owner of this post'
            ], 403);
        }
        // Validate the request
        $validatedData = $request->validated();
        $validatedData['notifiable'] = $request->input('notifiable') === 'true';
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
            if ($post->image) {
                Storage::delete('public/posts/' . $post->image);
            }
            $file = $request->file('image');
            $fileName = $post->id . '.' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/posts', $fileName); // Use Laravel storage for file storage
            $post->image = $fileName;

            //dispatch a job to check image content
            CheckImageContent::dispatch($post);
        }


        // Update other attributes
        $post->update($validatedData);

        return response()->json([
            'message' => 'Post created successfully'
        ]);
    }

    public function mutePost(Post $post)
    {
        $user = auth()->user();

    }


    public function destroy(Post $post)
    {
       //make sure the user is the owner of the post
        if (auth()->user()->id !== $post->user_id){
            return response()->json([
                'message' => 'You are not the owner of this post'
            ], 403);
        }
        $poster = $post->user;

        $communityId = $post->community_id;
        $experiencePoints = $poster->experiences()->where('community_id', $communityId)->value('experience_points');

        if ($experiencePoints>0){
            $decreaseAmount = min(25, $experiencePoints);
            $poster->addExperiencePoints(-$decreaseAmount, $communityId);
        }
        $post->delete();
        //revert exp gain from that post if no likes and do not if it is no the latest post
        return response()->json([
            'message' => 'Post deleted successfully'
        ]);
    }

    public function showDeletedPosts(){
        //only admin can view deleted post
        if (auth()->user()->type !== 'admin'){
            return response()->json([
                'message' => 'You are not an admin'
            ], 403);
        }
        $post= Post::onlyTrashed()->with('community','user')
            ->whereHas('user', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->paginate(10);
        return DeletedPostResource::collection($post);
    }
    public function showDeletedPost($id)
    {

        //only admin can view deleted post
        if (auth()->user()->type !== 'admin'){
            return response()->json([
                'message' => 'You are not an admin'
            ], 403);
        }

        $post = Post::onlyTrashed()->with('community','user')->find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Deleted post not found'
            ], 404);
        }

        return new DeletedPostResource($post);
    }
    public function showDeletedPostOnCommunity(Community $community)
    {
        //only admin can view deleted post
        if (auth()->user()->type !== 'admin'){
            return response()->json([
                'message' => 'You are not an admin'
            ], 403);
            }
        $post= Post::onlyTrashed()->with('community','user')->where('community_id', $community->id)->paginate(10);
        return DeletedPostResource::collection($post);
        }
    public function permanentDelete(Post $post)
    {
        //only admin can permanently delete a post
        if (auth()->user()->role !== 'admin'){
            return response()->json([
                'message' => 'You are not an admin'
            ], 403);
        }
        if ($post->image) {
            // Delete the image file
            Storage::delete('public/posts/' . $post->image);
        }

        if ($post->video) {
            // Delete the video file
            Storage::delete('public/posts/' . $post->video);
        }

        // Delete the post from the database
        $post->forceDelete();

        return response()->json([
            'message' => 'Post permanently deleted successfully'
        ]);

    }

    public function share(Post $post)
    {
        $sharer = auth()->user();

        $alreadyShared = $sharer->shares()->where('post_id', $post->id)->exists();
        $existingNotification = $post->user->notifications()
            ->where(function ($query) use ($sharer, $post) {
                $query->where('type', 'App\Notifications\PostShared')
                    ->whereJsonContains('data', [
                        'user_id' => $sharer->id,
                        'post_id' => $post->id
                    ]);
            })->exists();

        //they can share multiple time but ensure if same user share do not give poster the xp and notification


        $sharer->shares()->syncWithoutDetaching($post);


        if($post->notifiable && $post->user_id !== $sharer->id && !$existingNotification){
            $post->user->notify(new PostShared(New PostShareNotificationResource($post), $sharer));
            Cache::forget('unreadNotificationsCount-' . $post->user_id);
            broadcast(new PostInteraction($post, 'share'))->toOthers();
        }

        //give xp if new user share the post
        if (!$alreadyShared){
            $post->user->addExperiencePoints(5, $post->community_id);
            $post->updatePostSharesCount();
        }

        return response()->json([
            'message' => 'Post shared successfully',
        ]);
    }

    public function like(Post $post)
    {
        $liker = auth()->user();
//        $existingNotification = $post->user->notifications()->where('type', 'App\Notifications\PostLiked')->whereJsonContains('data',['post_id' => $post->id])->get();
////        ->exist();

        $existingNotification = $post->user->notifications()
            ->where(function ($query) use ($liker, $post) {
                $query->where('type', 'App\Notifications\PostLiked')
                    ->whereJsonContains('data', [
                        'user_id' => $liker->id,
                        'post_id' => $post->id
                    ]);
            })
            ->exists();
//        dd($existingNotification);
        if ($liker->likes()->where('post_id', $post->id)->exists()) {
            return response()->json([
                'message' => 'Post already liked'
            ], 403);
        }

        $liker->likes()->attach($post);
        $post->updatePostLikesCount();

        //send notification to the user who posted the post
        if ($post->notifiable && $post->user_id !== $liker->id && !$existingNotification) {
            $post->user->notify(new PostLiked(New PostLikeNotificationResource($post), $liker));
            Cache::forget('unreadNotificationsCount-' . $post->user_id);
//            broadcast(new PostLike( New PostLikeNotificationResource($post)))->toOthers();
            broadcast(new PostInteraction($post, 'like'))->toOthers();
        }

        //add xp to user who posted
        $post->user->addExperiencePoints(5, $post->community_id);

        return response()->json([
            'message' => 'Post liked successfully',
        ]);
    }


    public function unlike(Post $post)
    {
        $unliker = auth()->user();
        if (!$unliker->likes()->where('post_id', $post->id)->exists()) {
            return response()->json([
                'message' => 'Post not liked'
            ]);
        }
        $poster = $post->user;
        $communityId = $post->community_id;
        $experiencePoints = $poster->experiences()->where('community_id', $communityId)->value('experience_points');

        if($experiencePoints > 0){
            $decreaseAmount = min(5, $experiencePoints);
            $poster->addExperiencePoints(-$decreaseAmount, $communityId);
        }
        $unliker->likes()->detach($post);
        $post->updatePostLikesCount();
        return response()->json([
            'message' => 'Post unliked successfully',
        ]);
    }
}
