<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100">
        <div class="min-h-screen flex flex-col sm:justify-center items-center px-4 py-10 sm:py-12">
            <div class="mb-6">
                <a href="/">
                    <x-application-logo class="w-16 h-16 fill-current text-blue-600 dark:text-blue-400" />
                </a>
            </div>

            <div class="w-full max-w-md px-6 py-6 sm:px-7 sm:py-7 bg-white border border-slate-200 shadow-xl shadow-slate-200/40 rounded-2xl dark:bg-slate-900 dark:border-slate-800 dark:shadow-none">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
