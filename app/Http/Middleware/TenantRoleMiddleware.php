<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantRoleMiddleware
{
    public function handle(Request $request, Closure $next, $role, $permission = null): Response
    {
        $user = $request->user();
        $restaurant = $request->route('restaurant');

        // Check if user is super user (can access all)
        if ($user->isSuperUser()) {
            return $next($request);
        }

        // For restaurant owners and staff, check if they belong to the correct restaurant
        if ($restaurant && $user->restaurant_id !== $restaurant->id) {
            abort(403, 'Unauthorized access to this restaurant.');
        }

        // Check role
        if (!$user->hasRole($role)) {
            abort(403, 'Unauthorized role.');
        }

        // Check permission if provided
        if ($permission && !$user->hasPermissionTo($permission)) {
            abort(403, 'Unauthorized permission.');
        }

        return $next($request);
    }
} 