<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Tenant Dashboard</title>

    <script>
        window.TURNSTILE_SITE_KEY = @json(config('turnstile.site_key'));
        window.TENANT_THEME = @json(tenant('theme') ?? 'classic');
    </script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/tenant.js'])
</head>
<body class="font-sans antialiased">
    <div id="app">
        <tenant-layout>
            <router-view></router-view>
        </tenant-layout>
    </div>
</body>
</html> 