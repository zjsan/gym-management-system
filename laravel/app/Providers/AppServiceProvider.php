<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Gate::define('admin-only', function (User $user) {
            return $user->role?->slug === 'admin';
        });

        Gate::define('access-front-desk', function (User $user) {
            return $user->role?->slug === 'staff';
        });

        Gate::define('get-active-fee-rates', function (User $user){
            return in_array(strtolower(trim( $user->role?->slug ?? '')), ['admin','staff']);
        });

        //for the vue component to generate the reset password link with the frontend url
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
            return "{$frontendUrl}/reset-password?token={$token}&email={$notifiable->getEmailForPasswordReset()}";
        });
    } 
}
