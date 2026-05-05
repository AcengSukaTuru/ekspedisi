<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $title }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
            </div>

            <div class="flex gap-2">
                @if ($role === \App\Models\User::ROLE_ADMIN)
                    <a href="{{ route('admin.shipments.index') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Lihat Shipment</a>
                @elseif ($role === \App\Models\User::ROLE_CUSTOMER)
                    <a href="{{ route('customer.shipments.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Buat Shipment</a>
                @else
                    <a href="{{ route('courier.shipments.index') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Lihat Tugas</a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="mx-auto flex max-w-7xl flex-col gap-6">
            @include('partials.flash-message')

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($stats as $stat)
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <p class="text-sm text-slate-500">{{ $stat['label'] }}</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $stat['value'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-slate-900">Shipment Terbaru</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Tracking</th>
                                @if ($role === \App\Models\User::ROLE_ADMIN || $role === \App\Models\User::ROLE_COURIER)
                                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Customer</th>
                                @endif
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Rute</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Status</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Kendaraan</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($recentShipments as $shipment)
                                @php
                                    $detailRoute = match ($role) {
                                        \App\Models\User::ROLE_ADMIN => route('admin.shipments.show', $shipment),
                                        \App\Models\User::ROLE_COURIER => route('courier.shipments.show', $shipment),
                                        default => route('customer.shipments.show', $shipment),
                                    };
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ $shipment->tracking_number }}</td>
                                    @if ($role === \App\Models\User::ROLE_ADMIN || $role === \App\Models\User::ROLE_COURIER)
                                        <td class="px-4 py-3 text-slate-600">{{ $shipment->customer?->name ?? '-' }}</td>
                                    @endif
                                    <td class="px-4 py-3 text-slate-600">
                                        {{ $shipment->originBranch?->branch_name }} -> {{ $shipment->destinationBranch?->branch_name }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase text-slate-700">
                                            {{ $shipment->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">{{ $shipment->vehicle?->plate_number ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ $detailRoute }}" class="font-semibold text-slate-900 hover:text-slate-600">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $role === \App\Models\User::ROLE_CUSTOMER ? 5 : 6 }}" class="px-4 py-3 text-center text-slate-500">Belum ada data shipment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
