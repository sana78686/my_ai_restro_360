<?php

namespace App\Helpers;

class TenantHost
{
    /**
     * Tenant site hostname (no scheme/path). Port in tenant_domain_base applies to the final URL only.
     *
     * Examples: base "airestro360.com" → "slug.airestro360.com"; base "localhost:8000" → "slug.localhost".
     */
    public static function fqdn(string $subdomainOrSlug): string
    {
        $slug = strtolower(trim($subdomainOrSlug, '.'));
        $base = trim((string) config('tenancy.tenant_domain_base', 'localhost'), '.');

        $hostOnly = $base;
        if (preg_match('/^(.+):(\d+)$/', $base, $m)) {
            $hostOnly = $m[1];
        }

        return $slug.'.'.strtolower($hostOnly);
    }

    public static function loginUrl(string $subdomainOrSlug, ?string $fallbackId = null): string
    {
        $slug = trim((string) $subdomainOrSlug, '.');
        if ($slug === '' && $fallbackId !== null && $fallbackId !== '') {
            $slug = (string) $fallbackId;
        }
        $slug = strtolower($slug);

        $base = trim((string) config('tenancy.tenant_domain_base', 'localhost'), '.');

        $scheme = app()->environment('production') ? 'https' : request()->getScheme();

        $hostPart = self::fqdn($slug);
        if (preg_match('/^(.+):(\d+)$/', $base, $m)) {
            return $scheme.'://'.$hostPart.':'.$m[2].'/login';
        }

        return $scheme.'://'.$hostPart.'/login';
    }
}
