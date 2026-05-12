<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ \App\Helpers\AppNameHelper::getAppName() }}</title>
    <meta name="description" content="Register your restaurant on AiRestro 360. Online orders, operations, and guest service in one place.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('') }}/assets/logo/airestro360-favicon.png">

    <!-- Environment Variables -->
    <script>
        window.MAIN_DOMAIN = "{{ config('app.main_domain') }}";
        window.GOOGLE_SIGNIN_ENABLED = @json((bool) config('services.google.client_id'));
        window.TURNSTILE_SITE_KEY = @json(config('turnstile.site_key'));
    </script>

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app"></div>
</body>
</html>
