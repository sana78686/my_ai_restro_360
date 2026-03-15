<?php
namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant;

class TenantAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('tenant')->check()) {
            return response()->json([
                'message' => 'Unauthenticated.',
                'errors' => ['auth' => ['Please login to continue.']]
            ], 401);
        }

        return $next($request);
    }
} 