<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Password;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\URL;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // $this->registerSanctumPolicies();

        // $this->registerPasswordBroker();

        VerifyEmail::createUrlUsing(function ($notifiable) {
            $temporarySignedURL = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification())]

            );
            return str_replace(url('/api'), config('app.frontend_url'), $temporarySignedURL);
        });

        ResetPassword::createUrlUsing(function($notifiable, $token){
            $temporarySignedUrl = URL::temporarySignedRoute(
                'password.reset',
                now()->addMinutes(60),
                ['token' => $token,
                  'email' => $notifiable->getEmailForPasswordReset()
                    ]

            );
            return str_replace(url('/api'), config('app.frontend_url'), $temporarySignedUrl);
        });






        // Gate::define('update-post', function (User $user, Post $post) {
        //     return $user->id === $post->user_id;
        // });
    }
}
