<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
            return $user->role->slug? === 'staff';
        });
    }
}
