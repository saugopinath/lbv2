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
       
        if (method_exists(WorkFlowPermissionHelper::class,$permission)) {

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