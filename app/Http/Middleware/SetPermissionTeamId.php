<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

class SetPermissionTeamId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $schemeId = session('scheme_id');

            if (!$schemeId && session()->has('lgd_session.scheme_id')) {
                try {
                    $schemeId = Crypt::decryptString(session('lgd_session.scheme_id'));
                } catch (\Exception $e) {
                    $schemeId = null;
                }
            }

            if ($schemeId) {
                app(PermissionRegistrar::class)->setPermissionsTeamId((int) $schemeId);
            }
        }

        return $next($request);
    }
}
