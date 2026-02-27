<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Confirm password</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
            <a href="{{ route('login') }}" class="text-sm text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Back to sign in
            </a>

            <x-primary-button class="w-full sm:w-auto min-w-28">
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
