<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover, user-scalable=no">
    <meta name="theme-color" content="#eef2ff">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="EduSphere">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <title>@yield('title', 'EduSphere')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="edu-shell h-full min-h-dvh">
    @yield('body')
    @stack('scripts')
</body>
</html>
