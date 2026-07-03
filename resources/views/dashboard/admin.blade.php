<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $title }}</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.shipments.index') }}" class="btn-primary btn-sm">Kelola Shipment</a>
                <a href="{{ route('admin.payments.index') }}" class="btn-secondary btn-sm">Verifikasi Payment</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
            @foreach ($stats as $stat)
                <div class="card-hover p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">{{ $stat['label'] }}</p>
                            <p class="mt-3 text-2xl font-black tracking-tight text-slate-950 dark:text-white">{{ $stat['value'] }}</p>
                        </div>
                        <div class="rounded-2xl bg-blue-600/10 p-3 text-blue-600 dark:bg-blue-500/10 dark:text-blue-300">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M5 7l1.2 12.2A2 2 0 0 0 8.19 21h7.62a2 2 0 0 0 1.99-1.8L19 7M8 7V5a4 4 0 1 1 8 0v2" /></svg>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="card-hover overflow-hidden">
                <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-800">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Shipment Terbaru</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Monitoring order terbaru yang masuk ke sistem.</p>
                </div>
                <div class="table-wrapper rounded-none border-0">
                    <table class="table">
                        <thead>
                            <tr><th>Tracking</th><th>Customer</th><th>Rute</th><th>Status</th><th>Kurir</th><th></th></tr>
                        </thead>
                        <tbody>
                            @forelse ($recentShipments as $shipment)
                                <tr>
                                    <td>
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ $shipment->tracking_number }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $shipment->created_at?->format('d M Y') }}</p>
                                    </td>
                                    <td>{{ $shipment->customer?->name ?? '-' }}</td>
                                    <td>
                                        <p class="font-medium">{{ $shipment->originBranch?->city ?? '-' }} → {{ $shipment->destinationBranch?->city ?? '-' }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ ucfirst($shipment->service_type ?? 'regular') }}</p>
                                    </td>
                                    <td><x-status-badge :value="$shipment->statusLabel()" /></td>
                                    <td>
                                        <p class="font-medium">{{ $shipment->activeAssignment?->courier?->name ?? '-' }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $shipment->activeAssignment?->vehicle?->plate_number ?? $shipment->vehicle?->plate_number ?? 'Belum assigned' }}</p>
                                    </td>
                                    <td class="text-right"><a href="{{ route('admin.shipments.show', $shipment) }}" class="font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400">Detail</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-slate-500">Belum ada shipment.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Distribusi Status</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Persentase shipment berdasarkan status.</p>
                <div class="mt-5 space-y-4">
                    @foreach ($statusDistribution as $item)
                        <div>
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $item['label'] }}</span>
                                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">{{ $item['count'] }} · {{ $item['percentage'] }}%</span>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-slate-100 dark:bg-slate-800">
                                <div class="h-2 rounded-full bg-blue-600" style="width: {{ $item['percentage'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="card p-6 xl:col-span-2">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Butuh Tindakan</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Order yang perlu dicek admin.</p>
                    </div>
                    <a href="{{ route('admin.shipments.index') }}" class="btn-secondary btn-sm">Lihat semua</a>
                </div>
                <div class="mt-5 space-y-3">
                    @forelse ($attentionShipments as $shipment)
                        <a href="{{ route('admin.shipments.show', $shipment) }}" class="block rounded-2xl border border-slate-200 p-4 transition hover:border-blue-300 hover:bg-blue-50/40 dark:border-slate-800 dark:hover:border-blue-900 dark:hover:bg-blue-900/10">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-mono text-sm font-bold text-slate-950 dark:text-white">{{ $shipment->tracking_number }}</p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $shipment->customer?->name ?? '-' }} · {{ $shipment->originBranch?->city ?? '-' }} → {{ $shipment->destinationBranch?->city ?? '-' }}</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <x-status-badge :value="$shipment->statusLabel()" />
                                    @if (! $shipment->activeAssignment)
                                        <span class="badge bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Belum assigned</span>
                                    @endif
                                    @if ($shipment->payment?->payment_status === \App\Models\Payment::STATUS_PENDING)
                                        <span class="badge bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">Payment pending</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @empty
                        <p class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-500 dark:bg-slate-800/60 dark:text-slate-400">Tidak ada shipment yang perlu tindakan.</p>
                    @endforelse
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Payment Terbaru</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">Status pembayaran terakhir.</p>
                <div class="mt-5 space-y-3">
                    @forelse ($recentPayments as $payment)
                        <a href="{{ route('admin.payments.show', $payment) }}" class="block rounded-2xl bg-slate-50 p-4 transition hover:bg-blue-50 dark:bg-slate-800/60 dark:hover:bg-blue-900/20">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-950 dark:text-white">Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $payment->shipment?->tracking_number ?? '-' }}</p>
                                </div>
                                <x-status-badge :value="$payment->statusLabel()" />
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-slate-400">Belum ada payment.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['Kelola Shipment', 'Lihat dan proses semua order.', route('admin.shipments.index')],
                ['Verifikasi Payment', 'Cek bukti pembayaran masuk.', route('admin.payments.index')],
                ['Kelola Tarif', 'Atur harga rute dan layanan.', route('admin.rates.index')],
                ['Kelola Kendaraan', 'Pantau armada operasional.', route('admin.vehicles.index')],
            ] as [$label, $desc, $url])
                <a href="{{ $url }}" class="card-hover block p-5">
                    <p class="font-black text-slate-950 dark:text-white">{{ $label }}</p>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $desc }}</p>
                    <p class="mt-4 text-sm font-bold text-blue-600 dark:text-blue-400">Buka →</p>
                </a>
            @endforeach
        </div>
    </div>
</x-app-layout>
