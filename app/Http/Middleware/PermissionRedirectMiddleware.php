<?php
// app/Http/Middleware/PermissionRedirectMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\WorkFlowPermissionHelper;

class PermissionRedirectMiddleware
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!Auth::check()) {
            return redirect()->route('session.expired');
        }
        
        $user = Auth::user();
        
        // Check if the permission is a method in WorkFlowPermissionHelper
        if (method_exists(WorkFlowPermissionHelper::class, $permission)) {
            // Call the helper method dynamically
            if (!WorkFlowPermissionHelper::$permission()) {
                return redirect()
                    ->route('dashboard')
                    ->with('error', 'You do not have permission to access this page.');
            }
        } 
        // Check if it's a simple permission name
        else {
            // Check if user has the permission directly
            if (!$user->can($permission)) {
                return redirect()
                    ->route('dashboard')
                    ->with('error', 'You do not have permission to access this page.');
            }
        }
        
        return $next($request);
    }
}