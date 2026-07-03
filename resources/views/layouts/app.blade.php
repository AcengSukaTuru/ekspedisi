<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="theme" :class="{ 'dark': dark }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FastExpress') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            // Apply theme before Alpine starts. Prevents white flash and makes toggle instant.
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
    <body class="font-sans">
        @php
            $pageTitle = isset($header) ? trim(preg_replace('/\s+/', ' ', strip_tags((string) $header))) : 'Dashboard';
        @endphp

        <div x-data="{ sidebarOpen: false }" class="relative min-h-screen overflow-hidden bg-slate-50 text-slate-900 transition-colors duration-300 dark:bg-slate-950 dark:text-slate-100">
            <div class="pointer-events-none fixed inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,rgba(37,99,235,0.14),transparent_34%),radial-gradient(circle_at_bottom_right,rgba(14,165,233,0.12),transparent_30%)] dark:bg-[radial-gradient(circle_at_top_left,rgba(37,99,235,0.18),transparent_34%),radial-gradient(circle_at_bottom_right,rgba(14,165,233,0.10),transparent_30%)]"></div>
            <div class="pointer-events-none fixed inset-0 -z-10 opacity-[0.04] dark:opacity-[0.08]" style="background-image: linear-gradient(to right, currentColor 1px, transparent 1px), linear-gradient(to bottom, currentColor 1px, transparent 1px); background-size: 44px 44px;"></div>

            {{-- Mobile overlay --}}
            <div
                x-show="sidebarOpen"
                x-transition.opacity.duration.200ms
                class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm lg:hidden"
                @click="sidebarOpen = false"
                x-cloak
            ></div>

            @include('layouts.sidebar')

            <div class="lg:pl-72 transition-all duration-300">
                @include('layouts.navbar', ['pageTitle' => $pageTitle])

                <main class="p-4 sm:p-6 lg:p-8">
                    @isset($header)
                        <div class="mb-6">
                            {{ $header }}
                        </div>
                    @endisset

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>