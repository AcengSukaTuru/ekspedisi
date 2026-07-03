<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $title }}</h2>
                <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
            </div>
            <div class="flex gap-2">
                @if ($role === \App\Models\User::ROLE_ADMIN)
                    <a href="{{ route('admin.shipments.index') }}" class="btn-primary btn-sm">Lihat Shipment</a>
                @elseif ($role === \App\Models\User::ROLE_CUSTOMER)
                    <a href="{{ route('customer.shipments.create') }}" class="btn-primary btn-sm">Buat Shipment</a>
                @else
                    <a href="{{ route('courier.shipments.index') }}" class="btn-primary btn-sm">Lihat Tugas</a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        @include('partials.flash-message')

        {{-- Stats Cards --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($stats as $i => $stat)
                @php
                    $gradients = [
                        'from-blue-500 to-blue-600',
                        'from-indigo-500 to-indigo-600',
                        'from-emerald-500 to-emerald-600',
                        'from-amber-500 to-amber-600',
                        'from-rose-500 to-rose-600',
                        'from-purple-500 to-purple-600',
                    ];
                    $gradient = $gradients[$i % count($gradients)];
                @endphp
                <div class="card-hover group overflow-hidden">
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ $stat['label'] }}</p>
                                <p class="mt-1.5 text-3xl font-extrabold text-slate-900 dark:text-white">{{ $stat['value'] }}</p>
                            </div>
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br {{ $gradient }} shadow-lg opacity-80 group-hover:opacity-100 transition-opacity">
                                <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16.5 9.4l-9-5.19M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>
                            </div>
                        </div>
                    </div>
                    <div class="h-1 bg-gradient-to-r {{ $gradient }} opacity-60"></div>
                </div>
            @endforeach
        </div>

        {{-- Recent Shipments --}}
        <div class="card overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Shipment Terbaru</h3>
            </div>

            @php
                $statusLabels = \App\Models\Shipment::statusLabels();
                $statusColors = \App\Models\Shipment::statusColors();
            @endphp

            <div class="table-wrapper border-0 rounded-none">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tracking</th>
                            @if ($role !== \App\Models\User::ROLE_CUSTOMER)
                                <th>Customer</th>
                            @endif
                            <th>Rute</th>
                            <th>Status</th>
                            @if ($role !== \App\Models\User::ROLE_COURIER)
                                <th>Kendaraan</th>
                            @endif
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentShipments as $shipment)
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
                                <td>{{ $shipment->originBranch?->branch_name }} &rarr; {{ $shipment->destinationBranch?->branch_name }}</td>
                                <td>
                                    <span class="badge {{ $shipment->statusColor() }}">{{ $shipment->statusLabel() }}</span>
                                </td>
                                @if ($role !== \App\Models\User::ROLE_COURIER)
                                    <td>{{ $shipment->activeAssignment?->vehicle?->plate_number ?? '-' }}</td>
                                @endif
                                <td>
                                    <a href="{{ $detailRoute }}" class="font-semibold text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">Belum ada data shipment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
