<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'FastExpress') }} - Sistem Informasi Ekspedisi</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
        @php
            $features = [
                ['title' => 'Booking Shipment', 'desc' => 'Customer membuat pengiriman drop-off atau pickup request lengkap dengan data barang dan pembayaran.'],
                ['title' => 'Admin Control Center', 'desc' => 'Admin memproses order, assign kurir-kendaraan, update status, dan verifikasi pembayaran.'],
                ['title' => 'Courier Tracking', 'desc' => 'Kurir melihat tugas aktif, menambah tracking, dan mencatat delivery attempt.'],
                ['title' => 'Public Cek Resi', 'desc' => 'Pelanggan bisa pantau paket dari nomor resi tanpa login dengan data privat tetap aman.'],
            ];
            $steps = [
                ['Customer buat shipment', 'Pilih cabang, layanan, berat, pickup type, dan payment type.'],
                ['Admin assign operasional', 'Kurir dan kendaraan ditugaskan melalui ShipmentAssignment.'],
                ['Kurir update perjalanan', 'Tracking timeline mencatat posisi dan status paket.'],
                ['Paket diterima / retry', 'Delivered, failed delivery, reschedule, atau return to sender.'],
            ];
        @endphp

        <div class="pointer-events-none fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(37,99,235,0.16),transparent_34%),radial-gradient(circle_at_bottom_right,rgba(14,165,233,0.12),transparent_35%)]"></div>
            <div class="absolute inset-0 opacity-[0.04] dark:opacity-[0.08]" style="background-image: linear-gradient(#0f172a 1px, transparent 1px), linear-gradient(90deg, #0f172a 1px, transparent 1px); background-size: 34px 34px;"></div>
        </div>

        <div class="relative overflow-hidden">
            <header class="mx-auto flex max-w-7xl items-center justify-between px-4 py-5 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-600 text-sm font-black text-white shadow-lg shadow-blue-600/30">FE</span>
                    <span>
                        <span class="block text-base font-black tracking-tight text-slate-950 dark:text-white">FastExpress</span>
                        <span class="block text-xs font-semibold text-slate-500 dark:text-slate-400">Logistics Control System</span>
                    </span>
                </a>

                <nav class="hidden items-center gap-7 text-sm font-semibold text-slate-600 dark:text-slate-300 md:flex">
                    <a href="#fitur" class="hover:text-blue-600 dark:hover:text-blue-400">Fitur</a>
                    <a href="#alur" class="hover:text-blue-600 dark:hover:text-blue-400">Alur</a>
                    <a href="#demo" class="hover:text-blue-600 dark:hover:text-blue-400">Demo Akun</a>
                    <a href="{{ route('tracking.public') }}" class="hover:text-blue-600 dark:hover:text-blue-400">Cek Resi</a>
                </nav>

                <div class="flex items-center gap-2">
                    <a href="{{ route('login') }}" class="btn-secondary btn-sm">Login</a>
                    <a href="{{ route('register') }}" class="hidden btn-primary btn-sm sm:inline-flex">Register</a>
                </div>
            </header>

            <main>
                <section class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-12 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-8 lg:py-20">
                    <div>
                        <span class="inline-flex rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-bold uppercase tracking-[0.2em] text-blue-700 dark:border-blue-900/50 dark:bg-blue-900/20 dark:text-blue-300">Ekspedisi MVP Profesional</span>
                        <h1 class="mt-6 max-w-3xl text-5xl font-black tracking-[-0.04em] text-slate-950 dark:text-white sm:text-6xl lg:text-7xl">
                            Kirim barang, kelola kurir, dan pantau resi dalam satu sistem.
                        </h1>
                        <p class="mt-6 max-w-2xl text-base leading-8 text-slate-600 dark:text-slate-400">
                            FastExpress adalah aplikasi ekspedisi berbasis Laravel untuk customer, admin, dan kurir. Cocok untuk demo ujian: alur jelas, tampilan modern, tracking publik, pembayaran, dan assignment kendaraan.
                        </p>

                        <form method="GET" action="{{ route('tracking.public') }}" class="mt-8 max-w-2xl rounded-3xl border border-slate-200 bg-white/90 p-3 shadow-xl shadow-slate-200/70 backdrop-blur dark:border-slate-800 dark:bg-slate-900/80 dark:shadow-slate-950/30 sm:flex">
                            <input name="tracking_number" placeholder="Masukkan nomor resi, contoh EXP-260703-A1B2C3" class="input border-0 bg-transparent text-base focus:ring-0 sm:flex-1" required>
                            <button type="submit" class="btn-primary mt-3 w-full sm:mt-0 sm:w-auto">Cek Resi</button>
                        </form>

                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="{{ route('login') }}" class="btn-primary">Masuk Dashboard</a>
                            <a href="#demo" class="btn-secondary">Lihat Akun Demo</a>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-blue-500/20 blur-2xl"></div>
                        <div class="card p-5 shadow-2xl shadow-blue-950/10 dark:shadow-slate-950/50">
                            <div class="rounded-3xl bg-slate-950 p-5 text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-blue-300">Live Control</p>
                                        <p class="mt-1 text-2xl font-black">Dashboard Admin</p>
                                    </div>
                                    <span class="rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-bold text-emerald-300">Online</span>
                                </div>

                                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                                    @foreach ([['Total Shipment', '128'], ['Dalam Transit', '34'], ['Payment Pending', '12'], ['Delivered', '82']] as [$label, $value])
                                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                            <p class="text-xs text-slate-400">{{ $label }}</p>
                                            <p class="mt-2 text-3xl font-black">{{ $value }}</p>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-5 rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="font-bold">EXP-260703-A1B2C3</span>
                                        <span class="rounded-full bg-blue-400/15 px-2 py-1 text-xs font-bold text-blue-200">In Transit</span>
                                    </div>
                                    <div class="mt-4 flex items-center gap-2">
                                        @foreach ([0, 1, 2, 3, 4] as $i)
                                            <span class="h-2 flex-1 rounded-full {{ $i < 4 ? 'bg-blue-500' : 'bg-white/15' }}"></span>
                                        @endforeach
                                    </div>
                                    <p class="mt-3 text-xs text-slate-400">Jakarta → Bandung · Estimasi tiba besok</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="fitur" class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                    <div class="max-w-2xl">
                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-blue-600 dark:text-blue-400">Fitur Utama</p>
                        <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-950 dark:text-white sm:text-4xl">Semua kebutuhan operasional ekspedisi untuk demo yang meyakinkan.</h2>
                    </div>
                    <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        @foreach ($features as $feature)
                            <div class="card-hover p-6">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-600/10 text-blue-600 dark:bg-blue-500/10 dark:text-blue-300">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M5 7l1.2 12.2A2 2 0 0 0 8.19 21h7.62a2 2 0 0 0 1.99-1.8L19 7M8 7V5a4 4 0 1 1 8 0v2" /></svg>
                                </div>
                                <h3 class="mt-5 text-lg font-black text-slate-950 dark:text-white">{{ $feature['title'] }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $feature['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section id="alur" class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                    <div class="grid gap-8 lg:grid-cols-[0.8fr_1.2fr]">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.25em] text-blue-600 dark:text-blue-400">Alur Ekspedisi</p>
                            <h2 class="mt-3 text-3xl font-black tracking-tight text-slate-950 dark:text-white">Flow industri, tapi tetap simpel dipresentasikan.</h2>
                            <p class="mt-4 text-sm leading-7 text-slate-600 dark:text-slate-400">Status shipment mengikuti pola ekspedisi besar: created, picked up, hub, transit, out for delivery, delivered, failed delivery, sampai returned to sender.</p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach ($steps as $index => [$title, $desc])
                                <div class="card p-5">
                                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-sm font-black text-white">{{ $index + 1 }}</span>
                                    <h3 class="mt-4 font-black text-slate-950 dark:text-white">{{ $title }}</h3>
                                    <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $desc }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section id="demo" class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                    <div class="card overflow-hidden">
                        <div class="grid gap-0 lg:grid-cols-[0.9fr_1.1fr]">
                            <div class="bg-slate-950 p-8 text-white">
                                <p class="text-xs font-bold uppercase tracking-[0.25em] text-blue-300">Demo Ready</p>
                                <h2 class="mt-3 text-3xl font-black tracking-tight">Akun demo untuk semua role.</h2>
                                <p class="mt-4 text-sm leading-7 text-slate-300">Pakai akun ini saat presentasi untuk menunjukkan perbedaan akses admin, kurir, dan customer.</p>
                                <a href="{{ route('login') }}" class="mt-6 inline-flex rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-slate-950 hover:bg-blue-50">Login Sekarang</a>
                            </div>
                            <div class="p-6">
                                <div class="table-wrapper">
                                    <table class="table">
                                        <thead><tr><th>Role</th><th>Email</th><th>Password</th></tr></thead>
                                        <tbody>
                                            @foreach ([['Admin', 'admin@ekspedisi.test'], ['Courier', 'kurir1@ekspedisi.test'], ['Customer', 'customer@ekspedisi.test']] as [$role, $email])
                                                <tr><td class="font-bold">{{ $role }}</td><td class="font-mono text-xs">{{ $email }}</td><td class="font-mono text-xs">password</td></tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="mx-auto max-w-7xl px-4 py-8 text-sm text-slate-500 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-3 border-t border-slate-200 pt-6 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                    <p>© {{ now()->year }} FastExpress. Sistem Informasi Ekspedisi Online.</p>
                    <div class="flex gap-4 font-semibold">
                        <a href="{{ route('tracking.public') }}" class="hover:text-blue-600">Cek Resi</a>
                        <a href="{{ route('login') }}" class="hover:text-blue-600">Login</a>
                        <a href="{{ route('register') }}" class="hover:text-blue-600">Register</a>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
