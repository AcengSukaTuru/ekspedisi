<x-guest-layout>
    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Buat Akun Baru</h2>
    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Daftar untuk mulai menggunakan FastExpress.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Nama')" class="text-xs font-medium text-slate-600 dark:text-slate-400" />
            <x-text-input id="name" class="input mt-1 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-xs font-medium text-slate-600 dark:text-slate-400" />
            <x-text-input id="email" class="input mt-1 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="phone" :value="__('Telepon')" class="text-xs font-medium text-slate-600 dark:text-slate-400" />
                <x-text-input id="phone" class="input mt-1 block w-full" type="text" name="phone" :value="old('phone')" required autocomplete="tel" />
                <x-input-error :messages="$errors->get('phone')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="role" :value="__('Daftar Sebagai')" class="text-xs font-medium text-slate-600 dark:text-slate-400" />
                <select id="role" name="role" class="select mt-1 block w-full" required>
                    <option value="customer" @selected(old('role') === 'customer')>Customer</option>
                    <option value="courier" @selected(old('role') === 'courier')>Kurir</option>
                </select>
                <x-input-error :messages="$errors->get('role')" class="mt-1" />
            </div>
        </div>

        <div>
            <x-input-label for="address" :value="__('Alamat')" class="text-xs font-medium text-slate-600 dark:text-slate-400" />
            <textarea id="address" name="address" rows="2" class="textarea mt-1 block w-full" required>{{ old('address') }}</textarea>
            <x-input-error :messages="$errors->get('address')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" class="text-xs font-medium text-slate-600 dark:text-slate-400" />
            <x-text-input id="password" class="input mt-1 block w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" class="text-xs font-medium text-slate-600 dark:text-slate-400" />
            <x-text-input id="password_confirmation" class="input mt-1 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <button type="submit" class="btn-primary w-full">Daftar</button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500 dark:text-slate-400">
        Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-500 dark:text-blue-400">Masuk</a>
    </p>
</x-guest-layout>
