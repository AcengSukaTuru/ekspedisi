<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Riwayat Tracking</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $shipment->tracking_number }}</p>
            </div>
            <div class="flex gap-3">
                @if (auth()->user()->isCustomer())
                    <a href="{{ route('customer.shipments.show', $shipment) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Kembali</a>
                @elseif (auth()->user()->isCourier())
                    <a href="{{ route('courier.shipments.show', $shipment) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Kembali</a>
                    @if ($canCreate)
                        <a href="{{ $createRoute }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Tambah Tracking</a>
                    @endif
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @include('partials.flash-message')

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="space-y-4">
                    @forelse ($shipment->shipmentTrackings as $tracking)
                        <div class="rounded-xl border border-slate-200 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase text-slate-700">{{ $tracking->status }}</span>
                                <span class="text-xs text-slate-500">{{ optional($tracking->tracked_at)->format('d M Y H:i') }}</span>
                            </div>
                            <p class="mt-2 font-medium text-slate-900">{{ $tracking->location }}</p>
                            <p class="mt-1 text-sm text-slate-600">{{ $tracking->description ?: 'Tidak ada keterangan.' }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada riwayat tracking.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
