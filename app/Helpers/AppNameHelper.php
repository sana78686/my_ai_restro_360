<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class AppNameHelper
{
    /**
     * Get the application name based on tenant context
     *
     * @return string
     */
    public static function getAppName(): string
    {
        $defaultName = config('app.name', 'AiRestro360');

        try {
            // Check if we're in tenant context - multiple methods
            $isTenantContext = false;

            // Method 1: Check if tenant() helper returns a tenant
            try {
                $tenant = tenant();
                $isTenantContext = $tenant !== null;
            } catch (\Exception $e) {
                // tenant() might throw if tenancy not available
            }

            // Method 2: Check if tenancy is initialized
            if (!$isTenantContext) {
                try {
                    $isTenantContext = tenancy()->initialized ?? false;
                } catch (\Exception $e) {
                    // tenancy() might not be available
                }
            }

            // Method 3: Central vs tenant host (do not use app.name — it is not the hostname)
            if (!$isTenantContext) {
                $centralDomains = config('tenancy.central_domains', []);
                $host = request()->getHost();
                $port = (int) request()->getPort();
                $hostWithPort = ($port && ! in_array($port, [80, 443], true))
                    ? "{$host}:{$port}"
                    : $host;
                $isCentral = in_array($host, $centralDomains, true)
                    || in_array($hostWithPort, $centralDomains, true);
                $isTenantContext = ! $isCentral;
            }

            if ($isTenantContext) {
                try {
                    // When in tenant context, Setting model should use tenant database
                    $setting = Setting::first();
                    if ($setting && !empty(trim($setting->business_name ?? ''))) {
                        return trim($setting->business_name);
                    }
                } catch (\Exception $e) {
                    // If query fails, fall back to default
                    \Log::warning('Failed to get tenant business name: ' . $e->getMessage());
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Failed to determine app name: ' . $e->getMessage());
        }

        return $defaultName;
    }
}

