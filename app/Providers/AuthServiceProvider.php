<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
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
                'hash' => sha1($notifiable->getEmailForVerification())],



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




        //
    }
}
