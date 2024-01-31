<?php

namespace App\Observers;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class PostObserver
{
    public function clearCache(): void
    {
        for ($i = 1; $i <= 100; $i++) {
           $key = 'posts' . $i;
           if (Cache::has($key)) {
               Cache::forget($key);
           }else{
               break;
           }
        }
    }

    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        $this->clearCache();
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
