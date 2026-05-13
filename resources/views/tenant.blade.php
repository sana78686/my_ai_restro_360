<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ tenant('name') ?? config('app.name', 'Restaurant') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <script>
        window.MAIN_DOMAIN = "{{ config('app.main_domain') }}";
        window.TURNSTILE_SITE_KEY = @json(config('turnstile.site_key'));
        window.TENANT_THEME = @json(tenant('theme') ?? 'classic');
        window.TENANT_NAME = @json(tenant('name') ?? 'Restaurant');
        window.TENANT_LOGO = @json(tenant('logo_url') ?? tenant('logo'));
        window.TENANT_CURRENCY = @json(tenant('currency_symbol') ?? '$');
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div id="app"></div>
</body>
</html>