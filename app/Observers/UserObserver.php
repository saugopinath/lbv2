<?php

namespace App\Observers;

use App\Models\User;
use App\Jobs\IndexUserElasticSearchJob;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // unset($user->password_set_time);
        // unset($user->password_expires_at);
        // unset($user->last_otp_generation_time);
        // unset($user->last_otp_expire_time);
        // dispatch(new IndexUserElasticSearchJob($user));
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // dispatch(new IndexUserElasticSearchJob($user));
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // dispatch(new IndexUserElasticSearchJob($user));
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        // dispatch(new IndexUserElasticSearchJob($user));
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        // dispatch(new IndexUserElasticSearchJob($user));
    }
}
