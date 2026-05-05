<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $title }}</h2>

            @if ($role === \App\Models\User::ROLE_CUSTOMER)
                <a href="{{ route('customer.shipments.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Buat Shipment</a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="mx-auto max-w-7xl">
            @include('partials.flash-message')

            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Tracking</th>
                                @if ($role !== \App\Models\User::ROLE_CUSTOMER)
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Customer</th>
                                @endif
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Rute</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Layanan</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Biaya</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Pembayaran</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Status</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($shipments as $shipment)
                                @php
                                    $detailRoute = match ($role) {
                                        \App\Models\User::ROLE_ADMIN => route('admin.shipments.show', $shipment),
                                        \App\Models\User::ROLE_COURIER => route('courier.shipments.show', $shipment),
                                        default => route('customer.shipments.show', $shipment),
                                    };
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ $shipment->tracking_number }}</td>
                                    @if ($role !== \App\Models\User::ROLE_CUSTOMER)
                                        <td class="px-4 py-3 text-slate-600">{{ $shipment->customer?->name ?? '-' }}</td>
                                    @endif
                                    <td class="px-4 py-3 text-slate-600">{{ $shipment->originBranch?->branch_name }} -> {{ $shipment->destinationBranch?->branch_name }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $shipment->service_type }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-900">Rp {{ number_format((float) $shipment->shipping_cost, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase text-slate-700">{{ $shipment->payment?->payment_status ?? 'pending' }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase text-slate-700">{{ $shipment->status }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ $detailRoute }}" class="font-semibold text-slate-900">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $role !== \App\Models\User::ROLE_CUSTOMER ? 8 : 7 }}" class="px-4 py-3 text-center text-slate-500">Belum ada data shipment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3">
                    {{ $shipments->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
