<?php

namespace App\Providers;

use App\Models\Community;
use App\Models\Post;
use App\Models\User;
use App\Observers\CommunityPostObserver;
use App\Observers\UserPostObserver;
use Illuminate\Support\ServiceProvider;
use App\Observers\PostObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Post::observe(PostObserver::class);

    }
}
