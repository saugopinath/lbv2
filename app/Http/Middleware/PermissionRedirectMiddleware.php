<?php

namespace App\Http\Middleware;

use App\Helpers\WorkFlowPermissionHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionRedirectMiddleware
{
    public function handle(Request $request, Closure $next, $permission)
    {
        // if (!Auth::check() || !Auth::user()->can($permission)) {
        //     return redirect()
        //         ->route('dashboard')
        //         ->with('error', 'You do not have permission to access this page.');
        // }

        // return $next($request);

        if (method_exists(WorkFlowPermissionHelper::class, $permission)) {

            // Helper function call dynamically
            if (!WorkFlowPermissionHelper::$permission()) {
                return redirect()
                    ->route('dashboard')
                    ->with('error', 'You do not have permission to access this page.');
            }
        } else {
            // Helper এ method না থাকলে fallback Laravel permission check
            if (!Auth::check() || !Auth::user()->can($permission)) {
                return redirect()
                    ->route('dashboard')
                    ->with('error', 'You do not have permission to access this page.');
            }
        }

        return $next($request);
    }
}
