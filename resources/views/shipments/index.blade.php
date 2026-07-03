<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $title }}</h2>
            @if ($role === \App\Models\User::ROLE_CUSTOMER)
                <a href="{{ route('customer.shipments.create') }}" class="btn-primary btn-sm">Buat Shipment</a>
            @endif
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        @include('partials.flash-message')

        <div class="card overflow-hidden">
            <div class="table-wrapper border-0 rounded-none">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tracking</th>
                            @if ($role !== \App\Models\User::ROLE_CUSTOMER)
                                <th>Customer</th>
                            @endif
                            @if ($role === \App\Models\User::ROLE_ADMIN)
                                <th>Kurir</th>
                            @endif
                            <th>Rute</th>
                            <th>Layanan</th>
                            <th>Biaya</th>
                            <th>Pembayaran</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($shipments as $shipment)
                            @php
                                $detailRoute = match ($role) {
                                    \App\Models\User::ROLE_ADMIN => route('admin.shipments.show', $shipment),
                                    \App\Models\User::ROLE_COURIER => route('courier.shipments.show', $shipment),
                                    default => route('customer.shipments.show', $shipment),
                                };
                            @endphp
                            <tr>
                                <td class="font-semibold text-slate-900 dark:text-white">{{ $shipment->tracking_number }}</td>
                                @if ($role !== \App\Models\User::ROLE_CUSTOMER)
                                    <td>{{ $shipment->customer?->name ?? '-' }}</td>
                                @endif
                                @if ($role === \App\Models\User::ROLE_ADMIN)
                                    <td>{{ $shipment->activeAssignment?->courier?->name ?? '-' }}</td>
                                @endif
                                <td>{{ $shipment->originBranch?->branch_name }} &rarr; {{ $shipment->destinationBranch?->branch_name }}</td>
                                <td><span class="text-xs font-medium uppercase text-slate-500 dark:text-slate-400">{{ $shipment->service_type }}</span></td>
                                <td class="font-semibold text-slate-900 dark:text-white">Rp {{ number_format((float) $shipment->shipping_cost, 0, ',', '.') }}</td>
                                <td>
                                    @php $payStatus = $shipment->payment?->payment_status ?? 'pending'; @endphp
                                    <span class="badge {{ match($payStatus) { 'paid' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400', 'failed' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', default => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' } }}">{{ $payStatus }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $shipment->statusColor() }}">{{ $shipment->statusLabel() }}</span>
                                </td>
                                <td>
                                    <a href="{{ $detailRoute }}" class="font-semibold text-blue-600 hover:text-blue-500 dark:text-blue-400">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-400">Belum ada data shipment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($shipments->hasPages())
                <div class="border-t border-slate-200 px-6 py-4 dark:border-slate-800">
                    {{ $shipments->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
