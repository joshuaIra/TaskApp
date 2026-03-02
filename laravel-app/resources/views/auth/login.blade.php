<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Welcome back</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Sign in to continue to your workspace.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @php($viewErrors = isset($errors) ? $errors : new \Illuminate\Support\ViewErrorBag)

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$viewErrors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$viewErrors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-900" name="remember">
                <span class="ms-2 text-sm text-slate-600 dark:text-slate-300">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
            @if (Route::has('password.request'))
                <a class="text-sm text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="w-full sm:w-auto min-w-28">
                {{ __('Log in') }}
            </x-primary-button>
        </div>

        <p class="text-sm text-slate-600 dark:text-slate-300 pt-1">
            New here?
            <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Create account</a>
        </p>
    </form>
</x-guest-layout>
