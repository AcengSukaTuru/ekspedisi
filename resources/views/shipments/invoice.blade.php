<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Invoice {{ $shipment->tracking_number }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            @media print {
                @page { size: A4; margin: 14mm; }
                .print-hidden { display: none !important; }
                body { background: #fff !important; }
                .print-sheet { box-shadow: none !important; border: 0 !important; }
            }
        </style>
    </head>
    <body class="bg-slate-100 text-slate-900 antialiased">
        <div class="print-hidden mx-auto flex max-w-4xl items-center justify-between px-4 py-6">
            <a href="{{ url()->previous() }}" class="btn-secondary btn-sm">Kembali</a>
            <button onclick="window.print()" class="btn-primary btn-sm">Print Invoice</button>
        </div>

        <main class="mx-auto mb-10 max-w-4xl px-4 print:px-0">
            <section class="print-sheet rounded-3xl border border-slate-200 bg-white p-8 shadow-xl shadow-slate-200/60">
                <header class="flex flex-col gap-6 border-b border-slate-200 pb-6 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-600 text-sm font-black text-white">FE</span>
                        <div>
                            <h1 class="text-2xl font-black text-slate-950">FastExpress</h1>
                            <p class="text-sm font-semibold text-slate-500">Sistem Informasi Ekspedisi Online</p>
                        </div>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Invoice</p>
                        <p class="mt-1 font-mono text-lg font-black text-slate-950">INV-{{ $shipment->tracking_number }}</p>
                        <p class="mt-1 text-sm text-slate-500">Tanggal: {{ now()->format('d M Y') }}</p>
                    </div>
                </header>

                <div class="mt-8 grid gap-5 sm:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 p-5">
                        <p class="text-xs font-black uppercase tracking-wide text-slate-400">Ditagihkan Kepada</p>
                        <p class="mt-3 font-bold text-slate-950">{{ $shipment->customer?->name ?? '-' }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $shipment->customer?->email ?? '-' }}</p>
                        <p class="text-sm text-slate-600">{{ $shipment->customer?->phone ?? '-' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-5">
                        <p class="text-xs font-black uppercase tracking-wide text-slate-400">Detail Pengiriman</p>
                        <p class="mt-3 font-mono font-bold text-slate-950">{{ $shipment->tracking_number }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $shipment->originBranch?->city ?? '-' }} → {{ $shipment->destinationBranch?->city ?? '-' }}</p>
                        <p class="text-sm text-slate-600">{{ ucfirst($shipment->service_type ?? 'regular') }} · {{ number_format((float) $shipment->total_weight, 2, ',', '.') }} Kg</p>
                    </div>
                </div>

                <div class="mt-8 grid gap-5 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-slate-400">Pengirim</p>
                        <p class="mt-2 font-bold text-slate-950">{{ $shipment->sender_name }}</p>
                        <p class="text-sm text-slate-600">{{ $shipment->sender_phone }}</p>
                        <p class="text-sm text-slate-600">{{ $shipment->sender_address }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-wide text-slate-400">Penerima</p>
                        <p class="mt-2 font-bold text-slate-950">{{ $shipment->receiver_name }}</p>
                        <p class="text-sm text-slate-600">{{ $shipment->receiver_phone }}</p>
                        <p class="text-sm text-slate-600">{{ $shipment->receiver_address }}</p>
                    </div>
                </div>

                <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-black uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Barang</th>
                                <th class="px-4 py-3">Qty</th>
                                <th class="px-4 py-3">Berat</th>
                                <th class="px-4 py-3">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse ($shipment->shipmentItems as $item)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-slate-950">{{ $item->item_name }}</td>
                                    <td class="px-4 py-3">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3">{{ number_format((float) $item->weight, 2, ',', '.') }} Kg</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $item->description ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-6 text-center text-slate-500">Tidak ada item.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-8 flex justify-end">
                    <div class="w-full max-w-sm rounded-2xl bg-slate-950 p-5 text-white">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-300">Status Payment</span>
                            <span class="font-bold">{{ $shipment->payment?->statusLabel() ?? 'Menunggu' }}</span>
                        </div>
                        <div class="mt-4 flex items-end justify-between border-t border-white/10 pt-4">
                            <span class="text-sm text-slate-300">Total Ongkir</span>
                            <span class="text-2xl font-black">Rp {{ number_format((float) ($shipment->payment?->amount ?? $shipment->shipping_cost), 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <footer class="mt-8 border-t border-slate-200 pt-5 text-xs text-slate-500">
                    Invoice ini dibuat otomatis oleh sistem FastExpress. Simpan sebagai bukti transaksi dan pengiriman.
                </footer>
            </section>
        </main>
    </body>
</html>
