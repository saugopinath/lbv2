<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;
use Jenssegers\Agent\Agent;

class DetectSessionTimeout
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $agent = new Agent();
            $lastActivity = session('last_activity_time');
            // dd($lastActivity);
            if ($lastActivity) {
                $inactive = Carbon::parse($lastActivity)
                    ->diffInMinutes(now());
                $timeoutThreshold = (env('SESSION_LIFETIME') - 5);
                if ($inactive > $timeoutThreshold) {
                    $sessionId = session()->getId();
                    activity('auth')
                        ->causedBy($user)
                        ->event('logout')
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
                            'reason' => 'session_timeout',
                            'logout_time' => now(),
                        ])
                        ->log("{$user->name} logged out (Session Timeout)");
                    Auth::logout();
                    $UpdateUser = User::where('id', $user->id)->update([
                        'is_login' => 0,
                    ]);
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect()->route('session.expired');
                }
            }
            session(['last_activity_time' => now()]);
        }
        return $next($request);
    }
}
