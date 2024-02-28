<?php

namespace App\Observers;

use App\Models\Post;
use App\Models\Community;
use Illuminate\Support\Facades\Cache;

class PostObserver
{
    private function clearCache(Post $post,): void
    {
        //update the cache on liked post
        $community = $post->community;
        $cacheKey = 'community-posts-'.$community->id.'-';

        for ($page = 1; $page <=100; $page++){
            $cacheKey = $cacheKey.$page;
            Cache::forget($cacheKey);
        }

        $user = $post->user;
        $cacheKey = 'user-posts-'.$user->id.'-';
        for ($page = 1; $page <=100; $page++){
            $cacheKey = $cacheKey.$page;
            Cache::forget($cacheKey);
        }

        $cacheKey = 'homepage-posts-'.$user->id.'-';
        for ($page = 1; $page <=100; $page++){
            $cacheKey = $cacheKey.$page;
            Cache::forget($cacheKey);
        }

    }

    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post): void
    {
        $this->clearCache($post);
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        $this->clearCache($post);
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        $this->clearCache($post);
    }

    /**
     * Handle the Post "restored" event.
     */
//    public function restored(Post $post): void
//    {
//        //
//    }
//
//    /**
//     * Handle the Post "force deleted" event.
//     */
//    public function forceDeleted(Post $post): void
//    {
//        //
//    }
}
