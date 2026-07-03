<x-guest-layout>
    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Masuk ke Akun</h2>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Masukkan email dan password Anda.</p>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-xs font-medium text-slate-600 dark:text-slate-400" />
            <x-text-input id="email" class="input mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" class="text-xs font-medium text-slate-600 dark:text-slate-400" />
            <x-text-input id="password" class="input mt-1 block w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="flex items-center gap-2">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-800" name="remember">
                <span class="text-sm text-slate-600 dark:text-slate-400">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <button type="submit" class="btn-primary w-full">{{ __('Log in') }}</button>
    </form>

    @if (Route::has('register'))
        <p class="mt-6 text-center text-sm text-slate-500 dark:text-slate-400">
            Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-500 dark:text-blue-400">Daftar</a>
        </p>
    @endif
</x-guest-layout>
