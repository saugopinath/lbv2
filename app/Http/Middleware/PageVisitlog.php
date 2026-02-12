<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Auth;
use App\Models\UserPageVisitLog;
use App\Models\UserRoleSchemeOfficeMapping;
use Illuminate\Support\Facades\Log;

class PageVisitlog
{
    public function handle(Request $request, Closure $next)
    {

        // ✅ Skip spam requests
        if (
            $request->is('livewire/*') ||
            $request->is('api/*') ||
            $request->is('_debugbar/*') ||
            $request->is('css/*') ||
            $request->is('js/*') ||
            $request->is('images/*') ||
            $request->is('favicon.ico')
        ) {
            return $next($request);
        }

        $response = $next($request);

        try {
            $agent = new Agent();
            $browser = $agent->browser();
            $userId = Auth::id();
            $userRole = UserRoleSchemeOfficeMapping::where('user_id', $userId)->first()->role_id;
            UserPageVisitLog::create([
                'visit_time' => now(),
                'user_id' => $userId,
                'user_role_id' => $userRole,
                'ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
                'platform' => $agent->platform(),
                'browser' => $browser,
                'browser_version' => $agent->version($browser),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'referrer' => $request->headers->get('referer'),

            ]);
        } catch (\Exception $e) {

            Log::error('Page visit log failed: ' . $e->getMessage());
        }

        return $response;
    }
}
