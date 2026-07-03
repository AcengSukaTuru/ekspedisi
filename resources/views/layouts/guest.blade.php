<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="theme" :class="{ 'dark': dark }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FastExpress') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            (function() {
                if (localStorage.getItem('theme') === 'dark' ||
                    (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            })();

            document.addEventListener('alpine:init', () => {
                Alpine.store('theme', {
                    dark: document.documentElement.classList.contains('dark'),
                    toggle() {
                        this.dark = !this.dark;
                        localStorage.setItem('theme', this.dark ? 'dark' : 'light');
                        document.documentElement.classList.toggle('dark', this.dark);
                    }
                });
                Alpine.data('theme', () => ({
                    get dark() { return Alpine.store('theme').dark; },
                    toggle() { Alpine.store('theme').toggle(); }
                }));
            });
        </script>
        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="font-sans bg-gradient-to-br from-slate-50 via-sky-50 to-blue-100 text-slate-900 antialiased transition-colors duration-300 dark:from-slate-950 dark:via-slate-900 dark:to-blue-950 dark:text-slate-100">
        <div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden px-4 py-8">
            <div class="pointer-events-none absolute inset-0 opacity-[0.05] dark:opacity-[0.08]" style="background-image: linear-gradient(to right, currentColor 1px, transparent 1px), linear-gradient(to bottom, currentColor 1px, transparent 1px); background-size: 42px 42px;"></div>
            <div class="pointer-events-none absolute -left-32 top-16 h-80 w-80 rounded-full bg-blue-500/20 blur-3xl dark:bg-blue-500/10"></div>
            <div class="pointer-events-none absolute -right-32 bottom-16 h-80 w-80 rounded-full bg-sky-500/20 blur-3xl dark:bg-sky-500/10"></div>

            <div class="fixed right-4 top-4 z-10">
                <button @click="toggle()" class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white/80 text-slate-500 shadow-sm backdrop-blur-sm transition-all hover:bg-white hover:text-slate-900 dark:border-slate-700 dark:bg-slate-800/80 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white">
                    <svg x-show="dark" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                    <svg x-show="!dark" x-cloak class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                </button>
            </div>

            <div class="relative mb-6 text-center">
                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-sky-500 shadow-xl shadow-blue-600/20">
                    <svg class="h-7 w-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16.5 9.4l-9-5.19M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/>
                    </svg>
                </div>
                <h1 class="text-xl font-extrabold text-slate-900 dark:text-white">FastExpress</h1>
                <p class="text-xs font-semibold uppercase tracking-widest text-blue-600 dark:text-blue-400">Logistics Control</p>
            </div>

            <div class="relative w-full max-w-md">
                <div class="card-glass p-6 sm:p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>