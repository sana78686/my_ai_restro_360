<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class LaunchGate
{
    /**
     * Paths that are allowed without passing the gate (the gate page and unlock endpoint).
     */
    protected array $except = [
        'launch-gate',
        'launch-gate/unlock',
        // Public API used for marketing site / tenant signup before session unlock
        'api/app-name',
        'api/plans',
        'api/tenants/register',
        'api/tenant/find-by-email',
        'api/login/lookup',
        'api/check-subdomain',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('launch_gate.enabled', true)) {
            return $next($request);
        }

        // Storefront / tenant domains: do not require launch password (customers have no gate access)
        if (! $this->isCentralHost($request)) {
            return $next($request);
        }

        if ($this->isExcept($request)) {
            return $next($request);
        }

        if ($this->isUnlocked($request)) {
            return $next($request);
        }

        if ($request->expectsJson() || $request->is('api/*') || $request->is('*api*')) {
            return response()->json([
                'message' => 'Access restricted. Password required.',
                'require_launch_password' => true,
            ], 403);
        }

        return redirect()->guest('/launch-gate');
    }

    protected function isExcept(Request $request): bool
    {
        $path = $request->path();
        foreach ($this->except as $except) {
            if ($path === $except || str_starts_with($path, $except . '/')) {
                return true;
            }
        }
        return false;
    }

    protected function isUnlocked(Request $request): bool
    {
        return $request->session()->get('launch_gate_unlocked', false) === true;
    }

    /**
     * Hosts that serve the central (marketing / super-admin) app only.
     */
    protected function isCentralHost(Request $request): bool
    {
        $centralDomains = config('tenancy.central_domains', []);
        $host = $request->getHost();
        $port = (int) $request->getPort();
        $hostWithPort = ($port && ! in_array($port, [80, 443], true))
            ? "{$host}:{$port}"
            : $host;

        return in_array($host, $centralDomains, true)
            || in_array($hostWithPort, $centralDomains, true);
    }
}
