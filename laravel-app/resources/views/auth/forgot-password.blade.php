<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Forgot password</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            {{ __('No problem. Enter your email address and we will send you a reset link.') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
            <a href="{{ route('login') }}" class="text-sm text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Back to sign in
            </a>

            <x-primary-button class="w-full sm:w-auto min-w-44">
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
