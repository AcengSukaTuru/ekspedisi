<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Shipment</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $shipment->tracking_number }}</p>
            </div>
            <a href="{{ route($backRoute) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Kembali</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="mx-auto flex max-w-7xl flex-col gap-6">
            @include('partials.flash-message')

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
                    <h3 class="text-lg font-semibold text-slate-900">Informasi Shipment</h3>
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Customer</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $shipment->customer?->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Status</p>
                            <p class="mt-1"><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase text-slate-700">{{ $shipment->status }}</span></p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Asal</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $shipment->originBranch?->branch_name }} - {{ $shipment->originBranch?->city }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Tujuan</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $shipment->destinationBranch?->branch_name }} - {{ $shipment->destinationBranch?->city }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Layanan</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $shipment->service_type }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Kendaraan</p>
                            <p class="mt-1 font-medium text-slate-900">{{ $shipment->vehicle?->plate_number ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Tanggal Kirim</p>
                            <p class="mt-1 font-medium text-slate-900">{{ optional($shipment->shipment_date)->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Estimasi Tiba</p>
                            <p class="mt-1 font-medium text-slate-900">{{ optional($shipment->estimated_arrival)->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Total Berat</p>
                            <p class="mt-1 font-medium text-slate-900">{{ number_format((float) $shipment->total_weight, 2, ',', '.') }} Kg</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Biaya</p>
                            <p class="mt-1 font-medium text-slate-900">Rp {{ number_format((float) $shipment->shipping_cost, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">Pembayaran</h3>
                    <div class="mt-4 space-y-3 text-sm">
                        <div>
                            <p class="text-slate-500">Status Pembayaran</p>
                            <p class="font-semibold text-slate-900">{{ $shipment->payment?->payment_status ?? 'pending' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Nominal</p>
                            <p class="font-semibold text-slate-900">Rp {{ number_format((float) $shipment->payment?->amount, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Metode</p>
                            <p class="font-semibold text-slate-900">{{ $shipment->payment?->payment_method ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Tanggal Bayar</p>
                            <p class="font-semibold text-slate-900">{{ optional($shipment->payment?->payment_date)->format('d M Y') ?? '-' }}</p>
                        </div>

                        @if ($shipment->payment && auth()->user()->isAdmin())
                            <div class="pt-2">
                                <a href="{{ route('admin.payments.show', $shipment->payment) }}" class="text-sm font-semibold text-slate-900">Lihat Detail Payment</a>
                            </div>
                        @elseif ($shipment->payment && auth()->user()->isCustomer())
                            <div class="pt-2">
                                <a href="{{ route('customer.payments.show', $shipment->payment) }}" class="text-sm font-semibold text-slate-900">Lihat Detail Payment</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">Data Pengirim & Penerima</h3>
                    <div class="mt-4 grid gap-4 md:grid-cols-2 text-sm">
                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="font-semibold text-slate-900">Pengirim</p>
                            <p class="mt-2 text-slate-700">{{ $shipment->sender_name }}</p>
                            <p class="text-slate-600">{{ $shipment->sender_phone }}</p>
                            <p class="mt-2 text-slate-600">{{ $shipment->sender_address }}</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-4">
                            <p class="font-semibold text-slate-900">Penerima</p>
                            <p class="mt-2 text-slate-700">{{ $shipment->receiver_name }}</p>
                            <p class="text-slate-600">{{ $shipment->receiver_phone }}</p>
                            <p class="mt-2 text-slate-600">{{ $shipment->receiver_address }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <h3 class="text-lg font-semibold text-slate-900">Tracking Terbaru</h3>
                        @if (auth()->user()->isCustomer())
                            <a href="{{ route('customer.trackings.index', $shipment) }}" class="text-sm font-semibold text-slate-900">Lihat Semua</a>
                        @elseif (auth()->user()->isCourier())
                            <div class="flex gap-3">
                                <a href="{{ route('courier.trackings.index', $shipment) }}" class="text-sm font-semibold text-slate-900">Riwayat</a>
                                <a href="{{ route('courier.trackings.create', $shipment) }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Tambah Tracking</a>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 space-y-4">
                        @forelse ($shipment->shipmentTrackings->take(5) as $tracking)
                            <div class="rounded-xl border border-slate-200 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase text-slate-700">{{ $tracking->status }}</span>
                                    <span class="text-xs text-slate-500">{{ optional($tracking->tracked_at)->format('d M Y H:i') }}</span>
                                </div>
                                <p class="mt-2 font-medium text-slate-900">{{ $tracking->location }}</p>
                                <p class="mt-1 text-sm text-slate-600">{{ $tracking->description }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Belum ada tracking.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">Barang Kiriman</h3>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    @foreach ($shipment->shipmentItems as $item)
                        <div class="rounded-xl border border-slate-200 p-4">
                            <p class="font-semibold text-slate-900">{{ $item->item_name }}</p>
                            <p class="mt-2 text-sm text-slate-600">Jumlah: {{ $item->quantity }}</p>
                            <p class="text-sm text-slate-600">Berat: {{ number_format((float) $item->weight, 2, ',', '.') }} Kg</p>
                            <p class="mt-2 text-sm text-slate-600">{{ $item->description ?: 'Tidak ada deskripsi.' }}</p>
                            @if ($item->photo)
                                <img src="{{ asset('storage/'.$item->photo) }}" alt="{{ $item->item_name }}" class="mt-4 h-40 w-full rounded-xl object-cover">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            @if (auth()->user()->isAdmin())
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">Update Status Shipment</h3>
                    <form method="POST" action="{{ route('admin.shipments.update-status', $shipment) }}" class="mt-4 grid gap-4 md:grid-cols-2">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="status" class="text-sm font-medium text-slate-700">Status</label>
                            <select id="status" name="status" class="mt-1 w-full rounded-lg border-slate-300">
                                @foreach (['pending', 'processed', 'picked_up', 'in_transit', 'delivered'] as $status)
                                    <option value="{{ $status }}" @selected(old('status', $shipment->status) === $status)>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="vehicle_id" class="text-sm font-medium text-slate-700">Kendaraan</label>
                            <select id="vehicle_id" name="vehicle_id" class="mt-1 w-full rounded-lg border-slate-300">
                                <option value="">Belum ditentukan</option>
                                @foreach ($availableVehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" @selected(old('vehicle_id', $shipment->vehicle_id) == $vehicle->id)>{{ $vehicle->plate_number }} - {{ $vehicle->driver_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="estimated_arrival" class="text-sm font-medium text-slate-700">Estimasi Tiba</label>
                            <input id="estimated_arrival" name="estimated_arrival" type="date" value="{{ old('estimated_arrival', optional($shipment->estimated_arrival)->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-slate-300">
                        </div>
                        <div>
                            <label for="tracking_location" class="text-sm font-medium text-slate-700">Lokasi Tracking</label>
                            <input id="tracking_location" name="tracking_location" type="text" value="{{ old('tracking_location') }}" class="mt-1 w-full rounded-lg border-slate-300" placeholder="Contoh: Gudang Jakarta">
                        </div>
                        <div class="md:col-span-2">
                            <label for="tracking_description" class="text-sm font-medium text-slate-700">Catatan Tracking</label>
                            <textarea id="tracking_description" name="tracking_description" rows="3" class="mt-1 w-full rounded-lg border-slate-300" placeholder="Contoh: Shipment siap dikirim.">{{ old('tracking_description') }}</textarea>
                        </div>
                        <div class="md:col-span-2">
                            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
