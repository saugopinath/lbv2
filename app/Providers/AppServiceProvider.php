<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\AuthenticationInterface;
use App\Services\AuthenticationService;


use App\Interfaces\SendSmsInterface;
use App\Services\SendSmsService;
use App\Interfaces\JNMPAuthenticationInterface;
use App\Services\JNMPAuthenticationService;
use App\Interfaces\UserInterface;
use App\Services\UserService;

use App\Interfaces\ElasticsearchInterface;
use App\Services\ElasticsearchService;

use App\Interfaces\CmoAuthenticationInterface;
use App\Services\CmoAuthenticationService;

use App\Interfaces\DuplicatecheckInterface;
use App\Services\DuplicatecheckService;

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

use App\Contracts\AadhaarEncryptionServiceInterface;
use App\Services\AadhaarEncryptionService;

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

        $this->app->bind(
            JNMPAuthenticationInterface::class,
            JNMPAuthenticationService::class
        );

        $this->app->bind(DuplicatecheckInterface::class, DuplicatecheckService::class);

        $this->app->singleton(AadhaarEncryptionServiceInterface::class, function ($app) {
            return new AadhaarEncryptionService(
                url: config('services.adv.url', ''),
                apiKey: config('services.adv.key', ''),
                environment: config('app.env', 'production')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);

        if (config('scout.driver') === 'meilisearch') {

            $client = new \Meilisearch\Client(
                config('scout.meilisearch.host'),
                config('scout.meilisearch.key')
            );

            $index = $client->index('pension_beneficiary_personals');

            // ✅ Sortable attributes (must exist in searchable array)
            $index->updateSortableAttributes([
                'application_id',
                'scheme_id',
                'district_id',
                'created_at',
                'updated_at',
                'beneficiary_id',
                'application_id',
                'created_by_dist_code',
                'created_by_local_body_code',
                'next_level_role_id'
            ]);

            // ✅ Filterable attributes (must exist in searchable array)
            $index->updateFilterableAttributes([
                'scheme_id',
                'district_id',
                'rural_urban',
                'blockurban',
                'gpward',
                'next_level_role_id',
                'application_id',
                'beneficiary_id',
                'created_by_dist_code',
                'created_by_local_body_code',
            ]);
        }

        if (app()->environment('local', 'staging')) {
            \Illuminate\Support\Facades\Validator::extend('captcha', function ($attribute, $value, $parameters, $validator) {
                return true;
            });
        }
    }
}
