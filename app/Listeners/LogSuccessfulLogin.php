<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\User;
use App\Models\UserRoleSchemeOfficeMapping;
use Spatie\Activitylog\Models\Activity;
use Jenssegers\Agent\Agent;
use Stevebauman\Location\Facades\Location;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        if (!$event->user instanceof User) {
            return;
        }
        $sessionId = session()->getId();
        $exists = Activity::where('session_id', $sessionId)
            ->where('event', 'login')
            ->exists();
        if ($exists) {
            return;
        }
        $user = $event->user;
        $agent = new Agent();
        $mapping = UserRoleSchemeOfficeMapping::where('user_id', $user->id)
            ->select('role_id', 'office_id', 'scheme_id')
            ->first();
        $ip = app()->environment('local') ? '122.187.159.230' : request()->ip();
        $position = Location::get($ip);
        activity('auth')
            ->causedBy($user)
            ->event('login')
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
                'user_agent_raw' => request()->userAgent(),
                'login_time' => now(),
                'user_role' => $mapping?->role_id,
                'user_name' => $user->name,
                'user_mobile' => $user->mobile_no,
                'user_office_id' => $mapping?->office_id,
                'user_scheme_id' => $mapping?->scheme_id,
                'user_location' => [
                    'ip' => $ip,
                    'city' => $position->cityName ?? null,
                    'region' => $position->regionName ?? null,
                    'country' => $position->countryName ?? null,
                    'lat' => $position->latitude ?? null,
                    'long' => $position->longitude ?? null,
                ],
            ])
            ->log("{$user->name} logged in");
    }
}
