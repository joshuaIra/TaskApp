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
    @php
        $taskAppAuth = [
            'isAuthenticated' => auth()->check(),
            'user' => auth()->user() ? [
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'role' => auth()->user()->role,
            ] : null,
            'loginUrl' => route('login'),
            'registerUrl' => route('register'),
            'profileUrl' => route('profile.edit'),
            'logoutUrl' => route('logout'),
        ];
    @endphp
    <script id="taskapp-auth" type="application/json">{!! json_encode($taskAppAuth) !!}</script>
    <script>
        const authPayloadNode = document.getElementById('taskapp-auth');
        window.TaskAppAuth = authPayloadNode ? JSON.parse(authPayloadNode.textContent || '{}') : {
            isAuthenticated: false,
            user: null,
            loginUrl: '/login',
            registerUrl: '/register',
            profileUrl: '/profile',
            logoutUrl: '/logout',
        };
    </script>
    <div id="app"></div>
</body>
</html>
