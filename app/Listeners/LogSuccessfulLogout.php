<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;
use Jenssegers\Agent\Agent;
use Stevebauman\Location\Facades\Location;

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
        $ip = app()->environment('local') ? '122.187.159.230' : request()->ip();
        $position = Location::get($ip);

        activity('auth')
            ->causedBy($user)
            ->event('logout')
            ->tap(function ($activity) use ($sessionId) {
                $activity->session_id = $sessionId;
            })
            ->withProperties([
                'ip_address' => $ip,
                'browser' => $agent->browser(),
                'platform' => $agent->platform(),
                'device' => $agent->device(),
                'is_mobile' => $agent->isMobile(),
                'is_desktop' => $agent->isDesktop(),
                'session_id' => $sessionId,
                'logout_time' => now(),
                'user_location' => [
                    'ip' => $ip,
                    'city' => $position->cityName ?? null,
                    'region' => $position->regionName ?? null,
                    'country' => $position->countryName ?? null,
                    'latitude' => $position->latitude ?? null,
                    'longitude' => $position->longitude ?? null,
                ],
            ])
            ->log("{$user->name} logged out");
    }
}
