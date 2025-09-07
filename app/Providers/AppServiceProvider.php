<?php

namespace App\Providers;

use App\Models\User;
use App\Services\UserService;
use App\Observers\UserObserver;
use App\Models\BenRejectDetails;


use App\Services\SendSmsService;
use App\Interfaces\UserInterface;

use App\Models\BeneficiaryPersonal;
use App\Interfaces\SendSmsInterface;


use App\Services\ElasticsearchService;

use App\Services\AuthenticationService;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\ElasticsearchInterface;
use App\Interfaces\AuthenticationInterface;
use App\Observers\BenRejectDetailsObserver;
use App\Observers\BeneficiaryPersonalObserver;
use App\Models\DraftBeneficiaryPersonal;
use App\Observers\DraftBeneficiaryPersonalObserver;

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
        User::observe(UserObserver::class);
        BenRejectDetails::observe(BenRejectDetailsObserver::class);
        BeneficiaryPersonal::observe(BeneficiaryPersonalObserver::class);
        DraftBeneficiaryPersonal::observe(DraftBeneficiaryPersonalObserver::class);
    }
}
