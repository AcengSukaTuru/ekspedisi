<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $title }}</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        @include('partials.flash-message')

        <div class="card overflow-hidden">
            <div class="table-wrapper border-0 rounded-none">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tracking</th>
                            <th>Rute</th>
                            <th>Nominal</th>
                            <th>Tipe</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            @php
                                $paymentRoute = $role === \App\Models\User::ROLE_ADMIN
                                    ? route('admin.payments.show', $payment)
                                    : route('customer.payments.show', $payment);
                                $shipmentRoute = $role === \App\Models\User::ROLE_ADMIN
                                    ? route('admin.shipments.show', $payment->shipment)
                                    : route('customer.shipments.show', $payment->shipment);
                            @endphp
                            <tr>
                                <td class="font-semibold text-slate-900 dark:text-white">{{ $payment->shipment?->tracking_number }}</td>
                                <td>{{ $payment->shipment?->originBranch?->branch_name }} &rarr; {{ $payment->shipment?->destinationBranch?->branch_name }}</td>
                                <td class="font-semibold text-slate-900 dark:text-white">Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge {{ $payment->isCod() ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">{{ $payment->isCod() ? 'COD' : 'Prepaid' }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ match($payment->payment_status) { 'paid' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400', 'failed' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', default => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' } }}">{{ $payment->statusLabel() }}</span>
                                </td>
                                <td>{{ optional($payment->payment_date)->format('d M Y') ?? '-' }}</td>
                                <td>
                                    <div class="flex gap-3">
                                        <a href="{{ $paymentRoute }}" class="font-semibold text-blue-600 dark:text-blue-400">Detail</a>
                                        <a href="{{ $shipmentRoute }}" class="font-semibold text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">Shipment</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">Belum ada data pembayaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($payments->hasPages())
                <div class="border-t border-slate-200 px-6 py-4 dark:border-slate-800">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
