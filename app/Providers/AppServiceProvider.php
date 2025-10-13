<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

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
        // Configure Passport token expiration times
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        // TODO: Define OAuth2 scopes (to all db models)
        Passport::tokensCan([
            'read-movies' => 'Read movies information',
            'write-movies' => 'Create and update movies',
            'delete-movies' => 'Delete movies',
            'read-users' => '',
            'write-users' => '',
            'delete-users' => '',
            'rent-movies' => '',
            'admin' => 'Full administrative access',
        ]);

        // TODO: Define default scopes
        Passport::setDefaultScope([
            'read-movies',
            'rent-movies'
        ]);
    }
}
