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

use App\Interfaces\CmoAuthenticationInterface;
use App\Services\CmoAuthenticationService;
<<<<<<< HEAD

// use App\Interfaces\JaiBanglaInterface;
// use App\Services\JaiBanglaService;

=======
use App\Interfaces\JNMPAuthenticationInterface;
use App\Services\JNMPAuthenticationService;
>>>>>>> 1328cd4677af649027b42dc4d205a3292d482645
use App\Models\User;
use App\Observers\UserObserver;
use App\Models\AcceptRejectInfo;
use App\Observers\AcceptRejectInfoObserver;
use App\Models\DraftBeneficiaryPersonal;
use App\Observers\DraftBeneficiaryPersonalObserver;
use App\Models\BenRejectDetails;
use App\Observers\BenRejectDetailsObserver;
use App\Models\BeneficiaryPersonal;
use App\Observers\BeneficiaryPersonalObserver;

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

        $this->app->bind(CmoAuthenticationInterface::class, CmoAuthenticationService::class);

<<<<<<< HEAD
        // $this->app->bind(JaiBanglaInterface::class, JaiBanglaService::class);
=======
        $this->app->bind(
            JNMPAuthenticationInterface::class,
            JNMPAuthenticationService::class
        );
>>>>>>> 1328cd4677af649027b42dc4d205a3292d482645
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
<<<<<<< HEAD
        User::observe(UserObserver::class);
        AcceptRejectInfo::observe(AcceptRejectInfoObserver::class);
        BenRejectDetails::observe(BenRejectDetailsObserver::class);
        DraftBeneficiaryPersonal::observe(DraftBeneficiaryPersonalObserver::class);
        BeneficiaryPersonal::observe(BeneficiaryPersonalObserver::class);
=======
         User::observe(UserObserver::class);
         AcceptRejectInfo::observe(AcceptRejectInfoObserver::class);
         BenRejectDetails::observe(BenRejectDetailsObserver::class);
         DraftBeneficiaryPersonal::observe(DraftBeneficiaryPersonalObserver::class);
         BeneficiaryPersonal::observe(BeneficiaryPersonalObserver::class);

>>>>>>> 1328cd4677af649027b42dc4d205a3292d482645
    }
}
