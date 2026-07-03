<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Gate;
use App\Models\User;

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
        Gate::define('manage-blogs', function (User $user) {
            return $user->hasAnyRole(['superadmin', 'blog_posting', 'content_uploading']);
        });

        Gate::define('manage-leads', function (User $user) {
            return $user->hasAnyRole(['superadmin', 'leads_management']);
        });

        Gate::define('manage-content', function (User $user) {
            return $user->hasAnyRole(['superadmin', 'content_uploading']);
        });

        Gate::define('manage-tickets', function (User $user) {
            return $user->hasAnyRole(['superadmin', 'support']);
        });

        Gate::define('superadmin-only', function (User $user) {
            return $user->hasRole('superadmin');
        });

        Gate::define('manage-staff', function (User $user) {
            return $user->hasAnyRole(['superadmin', 'manage_staff']);
        });
    }
}
