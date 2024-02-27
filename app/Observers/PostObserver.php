<?php

namespace App\Observers;

use App\Models\Post;
use App\Models\Community;
use Illuminate\Support\Facades\Cache;

class PostObserver
{
    private function clearCache(Post $post): void
    {
        //update the cache on liked post

        $keys = [
            'posts-page-',
            'community-posts-',
            'homepage-posts-',
            'user-posts-'
        ];


        foreach ($keys as $key){
            for($i = 1; $i <= 100; $i++){
                $cacheKey = $key.$i;
                if(Cache::has($cacheKey)){
                    Cache::forget($cacheKey);
                }
                else{
                    break;
                }
            }
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
