<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionRedirectMiddleware
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!Auth::check() || !Auth::user()->can($permission)) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
