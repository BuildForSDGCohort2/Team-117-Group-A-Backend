<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

// Laravel Passport
use Laravel\Passport\Passport;

// Laravel Passport Multiauth
use Route;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
        Passport::routes();

         // Middleware `oauth.providers` middleware defined on $routeMiddleware above
         Route::group(['middleware' => 'oauth.providers'], function () {
            Passport::routes(function ($router) {
                return $router->forAccessTokens();
            });
        });
        Passport::tokensExpireIn(now()->addDays(1));

        Passport::refreshTokensExpireIn(now()->addDays(2));

        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
