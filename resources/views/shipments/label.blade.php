<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Label {{ $shipment->tracking_number }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            @media print {
                @page { size: A6 portrait; margin: 8mm; }
                .print-hidden { display: none !important; }
                body { background: #fff !important; }
                .print-label { box-shadow: none !important; border-color: #111827 !important; }
            }
        </style>
    </head>
    <body class="bg-slate-100 text-slate-900 antialiased">
        <div class="print-hidden mx-auto flex max-w-lg items-center justify-between px-4 py-6">
            <a href="{{ url()->previous() }}" class="btn-secondary btn-sm">Kembali</a>
            <button onclick="window.print()" class="btn-primary btn-sm">Print Label</button>
        </div>

        <main class="mx-auto max-w-lg px-4 print:px-0">
            <section class="print-label rounded-3xl border-2 border-slate-900 bg-white p-6 shadow-xl shadow-slate-200/70">
                <header class="flex items-center justify-between border-b-2 border-slate-900 pb-4">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-slate-950 text-sm font-black text-white">FE</span>
                        <div>
                            <h1 class="text-xl font-black text-slate-950">FastExpress</h1>
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Shipping Label</p>
                        </div>
                    </div>
                    <div class="text-right text-xs font-bold text-slate-500">
                        {{ ucfirst($shipment->service_type ?? 'regular') }}<br>
                        {{ number_format((float) $shipment->total_weight, 2, ',', '.') }} Kg
                    </div>
                </header>

                <div class="py-5 text-center">
                    <p class="text-xs font-black uppercase tracking-[0.25em] text-slate-500">Nomor Resi</p>
                    <p class="mt-2 font-mono text-2xl font-black tracking-tight text-slate-950">{{ $shipment->tracking_number }}</p>
                    <div class="mt-4 overflow-hidden rounded-lg border border-slate-900 bg-white px-2 py-3 font-mono text-xl font-black tracking-[0.35em] text-slate-950">
                        ||| {{ strtoupper(str_replace(['-', ' '], '', $shipment->tracking_number)) }} |||
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 border-y-2 border-slate-900 py-4">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wide text-slate-500">Asal</p>
                        <p class="mt-1 text-lg font-black text-slate-950">{{ $shipment->originBranch?->city ?? '-' }}</p>
                        <p class="text-xs text-slate-600">{{ $shipment->originBranch?->branch_name ?? '-' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-black uppercase tracking-wide text-slate-500">Tujuan</p>
                        <p class="mt-1 text-lg font-black text-slate-950">{{ $shipment->destinationBranch?->city ?? '-' }}</p>
                        <p class="text-xs text-slate-600">{{ $shipment->destinationBranch?->branch_name ?? '-' }}</p>
                    </div>
                </div>

                <div class="mt-5 space-y-4">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wide text-slate-500">Pengirim</p>
                        <p class="mt-1 font-bold text-slate-950">{{ $shipment->sender_name }}</p>
                        <p class="text-sm text-slate-600">{{ $shipment->sender_phone }}</p>
                        <p class="text-sm leading-5 text-slate-600">{{ $shipment->sender_address }}</p>
                    </div>
                    <div class="rounded-2xl border-2 border-slate-900 p-4">
                        <p class="text-[10px] font-black uppercase tracking-wide text-slate-500">Penerima</p>
                        <p class="mt-1 text-lg font-black text-slate-950">{{ $shipment->receiver_name }}</p>
                        <p class="text-sm font-semibold text-slate-700">{{ $shipment->receiver_phone }}</p>
                        <p class="mt-1 text-sm leading-5 text-slate-700">{{ $shipment->receiver_address }}</p>
                    </div>
                </div>

                <div class="mt-5 rounded-2xl bg-slate-100 p-4">
                    <p class="text-[10px] font-black uppercase tracking-wide text-slate-500">Isi Paket</p>
                    <p class="mt-1 text-sm font-bold text-slate-950">
                        {{ $shipment->shipmentItems->map(fn ($item) => $item->item_name.' x'.$item->quantity)->join(', ') ?: 'Barang kiriman' }}
                    </p>
                    <p class="mt-1 text-xs text-slate-600">Tanggal kirim: {{ optional($shipment->shipment_date)->format('d M Y') ?? '-' }}</p>
                </div>

                <footer class="mt-5 flex items-center justify-between border-t border-slate-300 pt-4 text-xs font-bold text-slate-500">
                    <span>Tempel di paket</span>
                    <span>{{ now()->format('d/m/Y H:i') }}</span>
                </footer>
            </section>
        </main>
    </body>
</html>
