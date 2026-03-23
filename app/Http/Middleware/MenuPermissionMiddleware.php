<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;

class MenuPermissionMiddleware
{
    public function handle(Request $request, Closure $next, $permission = null)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        $routeName = $request->route()->getName();

        // Get menu by route
        $menu = Menu::where('route', $routeName)->first();

        if (!$menu) {
            return $next($request);
        }

        // Convert JSON fields
        $departments = json_decode($menu->department_id, true) ?? [];
        $roles = json_decode($menu->role_id, true) ?? [];
        $permissions = json_decode($menu->permission_id, true) ?? [];

        // Check Department
        if (!in_array($user->department_id, $departments)) {
            abort(403, 'Department Access Denied');
        }

        // Check Role
        if (!in_array($user->role_id, $roles)) {
            abort(403, 'Role Access Denied');
        }

        // Check Permission
        if ($permission && !in_array($permission, $permissions)) {
            abort(403, 'Permission Denied');
        }

        return $next($request);
    }
}