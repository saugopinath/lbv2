<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\WorkFlowPermissionHelper;
use Illuminate\Support\Facades\Crypt;
use Spatie\Permission\PermissionRegistrar;

class PermissionRedirectMiddleware
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!Auth::check()) {
            return redirect()->route('session.expired');
        }

        $user = Auth::user();

        // scheme id from session or route
        $schemeId =
            session('scheme_id') ??
            $request->route('schemeId') ??
            $request->route('scheme_id');

        // Fallback to lgd_session if scheme_id is not set in root session or route
        if (!$schemeId && session()->has('lgd_session.scheme_id')) {
            try {
                $schemeId = Crypt::decryptString(session('lgd_session.scheme_id'));
            } catch (\Exception $e) {
                $schemeId = null;
            }
        }

        // Always set the team ID (even if null) to ensure strict scoping and avoid leakage
        app(PermissionRegistrar::class)
            ->setPermissionsTeamId($schemeId);

        // helper method check
        if (
            method_exists(
                WorkFlowPermissionHelper::class,
                $permission
            )
        ) {

            if (!WorkFlowPermissionHelper::$permission()) {

                return redirect()
                    ->route('dashboard')
                    ->with(
                        'error',
                        'No permission for this scheme.'
                    );
            }

        } else {

            if (!$user->can($permission)) {

                return redirect()
                    ->route('dashboard')
                    ->with(
                        'error',
                        'No permission for this scheme.'
                    );
            }
        }

        return $next($request);
    }
}