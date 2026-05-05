<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Sistem Informasi Ekspedisi Online') }}</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        @php
            $pageTitle = isset($header) ? trim(preg_replace('/\s+/', ' ', strip_tags((string) $header))) : 'Dashboard';
        @endphp

        <div x-data="{ sidebarOpen: false }" class="min-h-screen">
            <div
                x-show="sidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-30 bg-gray-900/40 lg:hidden"
                @click="sidebarOpen = false"
                x-cloak
            ></div>

            @include('layouts.sidebar')

            <div class="lg:pl-64">
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
