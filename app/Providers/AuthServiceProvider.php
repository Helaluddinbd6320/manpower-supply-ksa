<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::define('view-financial-fields', function ($user) {
            return $user->hasAnyRole(['super_admin', 'accounts_staff']);
        });

        Gate::define('edit-worker-profile', function ($user) {
            return $user->hasAnyRole(['super_admin', 'manager', 'office_staff']);
        });

        Gate::define('edit-placement-financials', function ($user) {
            return $user->hasAnyRole(['super_admin', 'accounts_staff']);
        });

        Gate::define('access-user-management', function ($user) {
            return $user->hasRole('super_admin');
        });
    }
}