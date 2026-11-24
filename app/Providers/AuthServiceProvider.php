<?php

namespace App\Providers;

use App\Auth\SpmbUserProvider;
use App\Models\ParentAccount;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

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
        Auth::provider('spmb', function ($app, array $config) {
            return new SpmbUserProvider($app['hash'], ParentAccount::class);
        });
    }
}
