<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;
use Jenssegers\Agent\Agent;

class LogSuccessfulLogout
{
    public function handle(Logout $event): void
    {
        if (!$event->user instanceof User) {
            return;
        }

        $sessionId = session()->getId();

        $exists = Activity::where('properties->session_id', $sessionId)
            ->where('event', 'logout')
            ->exists();

        if ($exists) {
            return;
        }

        $user = $event->user;
        $agent = new Agent();

        activity('auth')
            ->causedBy($user)
            ->event('logout')
            ->withProperties([
                'ip_address' => request()->ip(),
                'browser' => $agent->browser(),
                'platform' => $agent->platform(),
                'device' => $agent->device(),
                'is_mobile' => $agent->isMobile(),
                'is_desktop' => $agent->isDesktop(),
                'session_id' => $sessionId,
                'logout_time' => now(),
            ])
            ->log("{$user->name} logged out");
    }
}
