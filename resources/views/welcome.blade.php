<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sistem Informasi Ekspedisi Online</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="min-h-screen bg-gray-100 text-gray-900">
        <div class="mx-auto flex min-h-screen max-w-5xl flex-col justify-center px-6 py-10">
            <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm">
                <p class="text-sm font-semibold uppercase tracking-widest text-blue-600">FastExpress</p>
                <h1 class="mt-3 text-3xl font-bold">Sistem Informasi Ekspedisi Online</h1>
                <p class="mt-4 max-w-2xl text-gray-600">
                    Aplikasi untuk mengelola shipment, tracking, dan pembayaran berbasis role admin, customer, dan courier.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Login</a>
                    @endif

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">Register</a>
                    @endif
                </div>
            </div>
        </div>
    </body>
</html>
