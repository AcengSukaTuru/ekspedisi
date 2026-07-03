<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Cek Resi - {{ config('app.name', 'FastExpress') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
        <div class="pointer-events-none fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(37,99,235,0.14),transparent_34%),radial-gradient(circle_at_bottom_right,rgba(14,165,233,0.12),transparent_34%)]"></div>
            <div class="absolute inset-0 opacity-[0.04] dark:opacity-[0.08]" style="background-image: linear-gradient(#0f172a 1px, transparent 1px), linear-gradient(90deg, #0f172a 1px, transparent 1px); background-size: 34px 34px;"></div>
        </div>

        <main class="relative mx-auto min-h-screen max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <nav class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-600 text-sm font-black text-white shadow-lg shadow-blue-600/30">FE</span>
                    <span>
                        <span class="block text-sm font-black tracking-tight text-slate-950 dark:text-white">FastExpress</span>
                        <span class="block text-xs font-semibold text-slate-500 dark:text-slate-400">Public Tracking</span>
                    </span>
                </a>
                <div class="flex items-center gap-2">
                    <a href="{{ route('login') }}" class="btn-secondary btn-sm">Login</a>
                    <a href="{{ route('home') }}" class="btn-primary btn-sm">Beranda</a>
                </div>
            </nav>

            <section class="mx-auto mt-12 max-w-3xl text-center">
                <span class="inline-flex rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-bold uppercase tracking-[0.2em] text-blue-700 dark:border-blue-900/50 dark:bg-blue-900/20 dark:text-blue-300">Cek Resi</span>
                <h1 class="mt-5 text-4xl font-black tracking-tight text-slate-950 dark:text-white sm:text-5xl">Pantau posisi paket secara publik.</h1>
                <p class="mt-4 text-sm leading-6 text-slate-600 dark:text-slate-400">Masukkan nomor resi untuk melihat status pengiriman, rute, estimasi tiba, dan timeline tracking tanpa login.</p>

                <form method="GET" action="{{ route('tracking.public') }}" class="mt-8 rounded-3xl border border-slate-200 bg-white/90 p-3 shadow-xl shadow-slate-200/60 backdrop-blur dark:border-slate-800 dark:bg-slate-900/80 dark:shadow-slate-950/30 sm:flex">
                    <input name="tracking_number" value="{{ old('tracking_number', $trackingNumber) }}" placeholder="Contoh: EXP-260703-A1B2C3" class="input border-0 bg-transparent text-base focus:ring-0 sm:flex-1" required>
                    <button type="submit" class="btn-primary mt-3 w-full sm:mt-0 sm:w-auto">Lacak Paket</button>
                </form>
                @error('tracking_number')
                    <p class="mt-2 text-sm font-semibold text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </section>

            @if ($notFound)
                <section class="mx-auto mt-10 max-w-3xl">
                    <div class="card p-8 text-center">
                        <h2 class="text-xl font-black text-slate-950 dark:text-white">Resi tidak ditemukan</h2>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Periksa lagi nomor resi <span class="font-semibold text-slate-900 dark:text-white">{{ $trackingNumber }}</span>.</p>
                    </div>
                </section>
            @elseif ($shipment)
                @php
                    $latestTracking = $shipment->shipmentTrackings->last();
                    $progressStatuses = [
                        \App\Models\Shipment::STATUS_CREATED,
                        \App\Models\Shipment::STATUS_PICKED_UP,
                        \App\Models\Shipment::STATUS_AT_ORIGIN_HUB,
                        \App\Models\Shipment::STATUS_IN_TRANSIT,
                        \App\Models\Shipment::STATUS_AT_DEST_HUB,
                        \App\Models\Shipment::STATUS_OUT_FOR_DELIVERY,
                        \App\Models\Shipment::STATUS_DELIVERED,
                    ];
                    $currentIndex = array_search($shipment->status, $progressStatuses, true);
                    $isFailed = in_array($shipment->status, [\App\Models\Shipment::STATUS_FAILED_DELIVERY, \App\Models\Shipment::STATUS_RETURNED_TO_SENDER], true);
                @endphp

                <section class="mt-10 grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                    <div class="card p-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Nomor Resi</p>
                                <h2 class="mt-1 font-mono text-2xl font-black text-slate-950 dark:text-white">{{ $shipment->tracking_number }}</h2>
                            </div>
                            <span class="badge {{ $statusColors[$shipment->status] ?? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">{{ $shipment->statusLabel() }}</span>
                        </div>

                        <div class="mt-6 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/60">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Rute</p>
                                <p class="mt-2 text-sm font-bold text-slate-950 dark:text-white">{{ $shipment->originBranch?->city ?? '-' }} → {{ $shipment->destinationBranch?->city ?? '-' }}</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $shipment->originBranch?->branch_name ?? '-' }} ke {{ $shipment->destinationBranch?->branch_name ?? '-' }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/60">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Layanan</p>
                                <p class="mt-2 text-sm font-bold text-slate-950 dark:text-white">{{ ucfirst($shipment->service_type ?? 'regular') }}</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Estimasi tiba: {{ optional($shipment->estimated_arrival)->format('d M Y') ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <p class="text-sm font-bold text-slate-950 dark:text-white">Progress Pengiriman</p>
                            <div class="mt-4 flex items-center gap-1 overflow-x-auto pb-2">
                                @foreach ($progressStatuses as $i => $status)
                                    @php
                                        $reached = $currentIndex !== false && $i <= $currentIndex && ! $isFailed;
                                        $active = $shipment->status === $status && ! $isFailed;
                                        $dotText = $reached && ! $active ? '✓' : (string) ($i + 1);
                                    @endphp
                                    <div class="flex items-center">
                                        <div class="flex flex-col items-center">
                                            <div class="step-dot {{ $active ? 'step-dot-active' : ($reached ? 'step-dot-done' : 'step-dot-pending') }}">{{ $dotText }}</div>
                                            <p class="mt-1 w-16 text-center text-[10px] leading-tight {{ $active ? 'font-bold text-blue-600 dark:text-blue-400' : 'text-slate-400' }}">{{ $statusLabels[$status] ?? $status }}</p>
                                        </div>
                                        @if (! $loop->last)
                                            <div class="step-line {{ $reached && ! $active ? 'step-line-done' : 'step-line-pending' }}"></div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            @if ($isFailed)
                                <div class="mt-4 rounded-2xl bg-red-50 p-4 text-sm font-semibold text-red-700 dark:bg-red-900/20 dark:text-red-300">{{ $shipment->statusLabel() }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="card p-6">
                        <p class="text-sm font-bold text-slate-950 dark:text-white">Lokasi Terakhir</p>
                        <div class="mt-4 rounded-3xl bg-gradient-to-br from-blue-600 to-sky-500 p-5 text-white shadow-xl shadow-blue-600/20">
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-100">{{ optional($latestTracking?->tracked_at)->format('d M Y H:i') ?? 'Belum ada update' }}</p>
                            <p class="mt-3 text-2xl font-black">{{ $latestTracking?->location ?? $shipment->originBranch?->city ?? 'Gudang' }}</p>
                            <p class="mt-2 text-sm text-blue-50">{{ $latestTracking?->description ?? 'Shipment sudah terdaftar di sistem FastExpress.' }}</p>
                        </div>
                        <div class="mt-4 text-xs text-slate-500 dark:text-slate-400">Data publik disamarkan. Detail alamat, nomor telepon, dan pembayaran hanya tersedia untuk pemilik akun.</div>
                    </div>
                </section>

                <section class="mt-6 card p-6">
                    <h3 class="text-base font-black text-slate-950 dark:text-white">Timeline Tracking</h3>
                    <div class="mt-5 space-y-4">
                        @forelse ($shipment->shipmentTrackings as $tracking)
                            @php
                                $trackingLabel = $statusLabels[$tracking->status] ?? $tracking->status;
                                $trackingColor = $statusColors[$tracking->status] ?? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400';
                            @endphp
                            <div class="relative border-l-2 border-slate-200 pl-5 dark:border-slate-800">
                                <span class="absolute -left-[9px] top-1 h-4 w-4 rounded-full border-4 border-white bg-blue-600 dark:border-slate-900"></span>
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <span class="badge {{ $trackingColor }}">{{ $trackingLabel }}</span>
                                    <span class="text-xs font-semibold text-slate-400">{{ optional($tracking->tracked_at)->format('d M Y H:i') }}</span>
                                </div>
                                <p class="mt-2 text-sm font-bold text-slate-950 dark:text-white">{{ $tracking->location }}</p>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $tracking->description }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada tracking detail.</p>
                        @endforelse
                    </div>
                </section>
            @endif
        </main>
    </body>
</html>
