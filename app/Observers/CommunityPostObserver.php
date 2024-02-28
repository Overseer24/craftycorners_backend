<?php

namespace App\Observers;

use App\Models\Community;

class CommunityPostObserver
{

    public function clearCache(Community $community): void{

        $keys = [
            'community-posts-'.$community->id.'-'
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

    public function created(Community $community): void
    {
        $this->clearCache($community);
    }

    /**
     * Handle the Community "updated" event.
     */
    public function updated(Community $community): void
    {
        $this->clearCache($community);
    }

    /**
     * Handle the Community "deleted" event.
     */
    public function deleted(Community $community): void
    {
        $this->clearCache($community);
    }
}
    /**
     * Handle the Community "restored" event.
     */


    /**
     * Handle the Community "force deleted" event.*/


