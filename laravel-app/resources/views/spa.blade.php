<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="TaskApp is a role-based task management website for teams to plan, assign, track, and complete tasks with real-time visibility.">
    <title>TaskApp - Manage Your Tasks</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <script>
        window.TaskAppAuth = {
            isAuthenticated: @json(auth()->check()),
            user: @json(auth()->user() ? ['name' => auth()->user()->name, 'email' => auth()->user()->email, 'role' => auth()->user()->role] : null),
            loginUrl: @json(route('login')),
            registerUrl: @json(route('register')),
            profileUrl: @json(route('profile.edit')),
            logoutUrl: @json(route('logout')),
        };
    </script>
    <div id="app"></div>
</body>
</html>
