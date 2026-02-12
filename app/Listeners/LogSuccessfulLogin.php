<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;
use Jenssegers\Agent\Agent;

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
        $sessionId = session()->getId();
        activity('auth')
            ->causedBy($user)
            ->event('login')
            ->tap(function ($activity) use ($sessionId) {
                $activity->session_id = $sessionId;
            })
            ->withProperties([
                'ip_address' => request()->ip(),
                'browser' => $agent->browser(),
                'platform' => $agent->platform(),
                'device' => $agent->device(),
                'is_mobile' => $agent->isMobile(),
                'is_desktop' => $agent->isDesktop(),
                'session_id' => $sessionId,
                'user_agent_raw' => request()->userAgent(),
                'login_time' => now(),
            ])
            ->log("{$user->name} logged in");
    }
}
