<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $title }}</h2>
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
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Rute</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Nominal</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Status</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Tanggal Bayar</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
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
                                    <td class="px-4 py-3 font-medium text-slate-900">{{ $payment->shipment?->tracking_number }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $payment->shipment?->originBranch?->branch_name }} -> {{ $payment->shipment?->destinationBranch?->branch_name }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-900">Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase text-slate-700">{{ $payment->payment_status }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">{{ optional($payment->payment_date)->format('d M Y') ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-3">
                                            <a href="{{ $paymentRoute }}" class="font-semibold text-slate-900">Detail Payment</a>
                                            <a href="{{ $shipmentRoute }}" class="font-semibold text-slate-500">Lihat Shipment</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-3 text-center text-slate-500">Belum ada data pembayaran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
