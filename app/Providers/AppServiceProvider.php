<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\AuthenticationInterface;
use App\Services\AuthenticationService;


use App\Interfaces\SendSmsInterface;
use App\Services\SendSmsService;

use App\Interfaces\UserInterface;
use App\Services\UserService;

use App\Interfaces\ElasticsearchInterface;
use App\Services\ElasticsearchService;

use App\Models\User;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthenticationInterface::class, AuthenticationService::class);
       
        $this->app->bind(SendSmsInterface::class, SendSmsService::class);

        $this->app->bind(UserInterface::class, UserService::class);

        $this->app->bind(ElasticsearchInterface::class, ElasticsearchService::class);

        
       
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // User::observe(UserObserver::class);
    }
}
