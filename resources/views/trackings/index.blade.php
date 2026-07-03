<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Riwayat Tracking</h2>
                <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">{{ $shipment->tracking_number }}</p>
            </div>
            <div class="flex gap-2">
                @if (auth()->user()->isCustomer())
                    <a href="{{ route('customer.shipments.show', $shipment) }}" class="btn-secondary btn-sm">Kembali</a>
                @elseif (auth()->user()->isCourier())
                    <a href="{{ route('courier.shipments.show', $shipment) }}" class="btn-secondary btn-sm">Kembali</a>
                    @if ($canCreate && !$shipment->isFinal())
                        <a href="{{ $createRoute }}" class="btn-primary btn-sm">Tambah Tracking</a>
                    @endif
                @endif
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-5xl space-y-6">
        @include('partials.flash-message')

        @php
            $statusLabels = \App\Models\Shipment::statusLabels();
            $statusColors = \App\Models\Shipment::statusColors();
        @endphp

        <div class="card p-4">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="text-sm text-slate-500 dark:text-slate-400">Status:</span>
                <span class="badge {{ $shipment->statusColor() }}">{{ $shipment->statusLabel() }}</span>
                <span class="text-slate-300 dark:text-slate-700">&middot;</span>
                <span class="text-sm text-slate-500 dark:text-slate-400">{{ $shipment->originBranch?->branch_name }} &rarr; {{ $shipment->destinationBranch?->branch_name }}</span>
            </div>
        </div>

        <div class="card p-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Timeline</h3>
            <div class="space-y-0">
                @forelse ($shipment->shipmentTrackings as $i => $tracking)
                    <div class="relative flex gap-4 pb-6 {{ $loop->last ? '' : 'border-l-2 border-slate-200 dark:border-slate-800 ml-3' }}">
                        <div class="absolute -left-[7px] top-1 flex h-3 w-3 items-center justify-center">
                            <div class="h-3 w-3 rounded-full {{ $i === 0 ? 'bg-blue-500 ring-4 ring-blue-500/20' : 'bg-slate-300 dark:bg-slate-700' }}"></div>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="flex items-center justify-between gap-3">
                                <span class="badge {{ $statusColors[$tracking->status] ?? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">{{ $statusLabels[$tracking->status] ?? $tracking->status }}</span>
                                <span class="text-[10px] text-slate-400">{{ optional($tracking->tracked_at)->format('d M Y H:i') }}</span>
                            </div>
                            <p class="mt-1.5 text-sm font-medium text-slate-900 dark:text-white">{{ $tracking->location }}</p>
                            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ $tracking->description ?: '-' }}</p>
                            @if ($tracking->proof_path)
                                <a href="{{ asset('storage/'.$tracking->proof_path) }}" target="_blank" class="mt-1 inline-block text-xs font-semibold text-blue-600 dark:text-blue-400">Lihat Foto</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Belum ada riwayat tracking.</p>
                @endforelse
            </div>
        </div>

        @if ($shipment->deliveryAttempts->count() > 0)
            <div class="card p-6">
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Riwayat Pengiriman</h3>
                <div class="space-y-3">
                    @foreach ($shipment->deliveryAttempts as $attempt)
                        <div class="rounded-xl border p-4 {{ $attempt->isSuccessful() ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-900/10' : 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/10' }}">
                            <div class="flex items-center justify-between">
                                <span class="badge {{ $attempt->isSuccessful() ? 'bg-emerald-200 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-red-200 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">#{{ $attempt->attempt_number }} &mdash; {{ $attempt->isSuccessful() ? 'Berhasil' : 'Gagal' }}</span>
                                <span class="text-xs text-slate-400">{{ optional($attempt->attempted_at)->format('d M Y H:i') }}</span>
                            </div>
                            @if ($attempt->isFailed() && $attempt->failure_reason)
                                <p class="mt-2 text-sm text-red-700 dark:text-red-400">Alasan: {{ $attempt->failureReasonLabel() }}</p>
                            @endif
                            @if ($attempt->notes)
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $attempt->notes }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
