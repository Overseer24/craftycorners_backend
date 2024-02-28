<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserPostObserver
{

    public function cacheClear(User $user)
    {

        $keys = [
            'user-posts-' . $user->id . '-'
        ];

        foreach ($keys as $key) {
            for ($i = 1; $i <= 100; $i++) {
                $cacheKey = $key . $i;
                if (Cache::has($cacheKey)) {
                    Cache::forget($cacheKey);
                } else {
                    break;
                }
            }
        }
    }


    public function created(User $user): void
    {
        $this->cacheClear($user);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $this->cacheClear($user);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $this->cacheClear($user);
    }

}



