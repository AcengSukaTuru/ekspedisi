<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Buat Shipment Baru</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @include('partials.flash-message')

            <div class="grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <form method="POST" action="{{ route('customer.shipments.store') }}" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2">
                        @csrf
                        <div class="md:col-span-2 rounded-2xl bg-slate-50 p-4 text-sm text-slate-600">
                            <p class="font-semibold text-slate-900">Alur otomatis saat shipment dibuat</p>
                            <p class="mt-2">Sistem akan membuat nomor tracking unik, menghitung ongkir dari tarif cabang, membuat data pembayaran, dan menambahkan tracking awal secara otomatis.</p>
                        </div>

                        <div>
                            <label for="origin_branch_id" class="text-sm font-medium text-slate-700">Cabang Asal</label>
                            <select id="origin_branch_id" name="origin_branch_id" class="mt-1 w-full rounded-lg border-slate-300" required>
                                <option value="">Pilih cabang</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" @selected(old('origin_branch_id') == $branch->id)>{{ $branch->branch_name }} - {{ $branch->city }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="destination_branch_id" class="text-sm font-medium text-slate-700">Cabang Tujuan</label>
                            <select id="destination_branch_id" name="destination_branch_id" class="mt-1 w-full rounded-lg border-slate-300" required>
                                <option value="">Pilih cabang</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" @selected(old('destination_branch_id') == $branch->id)>{{ $branch->branch_name }} - {{ $branch->city }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="service_type" class="text-sm font-medium text-slate-700">Jenis Layanan</label>
                            <select id="service_type" name="service_type" class="mt-1 w-full rounded-lg border-slate-300" required>
                                @foreach ($serviceTypes as $serviceType)
                                    <option value="{{ $serviceType }}" @selected(old('service_type') === $serviceType)>{{ ucfirst($serviceType) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="total_weight" class="text-sm font-medium text-slate-700">Total Berat (Kg)</label>
                            <input id="total_weight" name="total_weight" type="number" min="0.1" step="0.01" value="{{ old('total_weight') }}" class="mt-1 w-full rounded-lg border-slate-300" required>
                        </div>
                        <div>
                            <label for="shipment_date" class="text-sm font-medium text-slate-700">Tanggal Kirim</label>
                            <input id="shipment_date" name="shipment_date" type="date" value="{{ old('shipment_date', now()->toDateString()) }}" class="mt-1 w-full rounded-lg border-slate-300">
                        </div>
                        <div>
                            <label for="estimated_arrival" class="text-sm font-medium text-slate-700">Estimasi Tiba</label>
                            <input id="estimated_arrival" name="estimated_arrival" type="date" value="{{ old('estimated_arrival', now()->addDays(3)->toDateString()) }}" class="mt-1 w-full rounded-lg border-slate-300">
                        </div>

                        <div>
                            <label for="sender_name" class="text-sm font-medium text-slate-700">Nama Pengirim</label>
                            <input id="sender_name" name="sender_name" type="text" value="{{ old('sender_name', auth()->user()->name) }}" class="mt-1 w-full rounded-lg border-slate-300" required>
                        </div>
                        <div>
                            <label for="sender_phone" class="text-sm font-medium text-slate-700">Telepon Pengirim</label>
                            <input id="sender_phone" name="sender_phone" type="text" value="{{ old('sender_phone', auth()->user()->customer?->phone) }}" class="mt-1 w-full rounded-lg border-slate-300" required>
                        </div>
                        <div class="md:col-span-2">
                            <label for="sender_address" class="text-sm font-medium text-slate-700">Alamat Pengirim</label>
                            <textarea id="sender_address" name="sender_address" rows="3" class="mt-1 w-full rounded-lg border-slate-300" required>{{ old('sender_address', auth()->user()->customer?->address) }}</textarea>
                        </div>

                        <div>
                            <label for="receiver_name" class="text-sm font-medium text-slate-700">Nama Penerima</label>
                            <input id="receiver_name" name="receiver_name" type="text" value="{{ old('receiver_name') }}" class="mt-1 w-full rounded-lg border-slate-300" required>
                        </div>
                        <div>
                            <label for="receiver_phone" class="text-sm font-medium text-slate-700">Telepon Penerima</label>
                            <input id="receiver_phone" name="receiver_phone" type="text" value="{{ old('receiver_phone') }}" class="mt-1 w-full rounded-lg border-slate-300" required>
                        </div>
                        <div class="md:col-span-2">
                            <label for="receiver_address" class="text-sm font-medium text-slate-700">Alamat Penerima</label>
                            <textarea id="receiver_address" name="receiver_address" rows="3" class="mt-1 w-full rounded-lg border-slate-300" required>{{ old('receiver_address') }}</textarea>
                        </div>

                        <div>
                            <label for="item_name" class="text-sm font-medium text-slate-700">Nama Barang</label>
                            <input id="item_name" name="item_name" type="text" value="{{ old('item_name') }}" class="mt-1 w-full rounded-lg border-slate-300" required>
                        </div>
                        <div>
                            <label for="quantity" class="text-sm font-medium text-slate-700">Jumlah</label>
                            <input id="quantity" name="quantity" type="number" min="1" value="{{ old('quantity', 1) }}" class="mt-1 w-full rounded-lg border-slate-300" required>
                        </div>
                        <div class="md:col-span-2">
                            <label for="description" class="text-sm font-medium text-slate-700">Deskripsi Barang</label>
                            <textarea id="description" name="description" rows="3" class="mt-1 w-full rounded-lg border-slate-300">{{ old('description') }}</textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label for="photo" class="text-sm font-medium text-slate-700">Foto Barang</label>
                            <input id="photo" name="photo" type="file" class="mt-1 w-full rounded-lg border-slate-300">
                            <p class="mt-1 text-xs text-slate-500">Opsional. Maksimal 2 MB.</p>
                        </div>

                        <div class="md:col-span-2 flex items-center justify-between rounded-xl bg-slate-900 px-4 py-4 text-sm text-slate-200">
                            <span>Ongkir akan dihitung otomatis: tarif per kg x total berat.</span>
                            <button type="submit" class="rounded-lg bg-white px-4 py-2 font-semibold text-slate-900">Simpan Shipment</button>
                        </div>
                    </form>
                </div>

                <div class="space-y-6">
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-slate-900">Panduan Singkat</h3>
                        <ul class="mt-4 space-y-3 text-sm text-slate-600">
                            <li>Pastikan cabang asal dan tujuan berbeda.</li>
                            <li>Pilih layanan `regular` atau `express` sesuai tarif yang tersedia.</li>
                            <li>Jika tarif belum ada, sistem akan menampilkan pesan error yang jelas.</li>
                            <li>Setelah disimpan, shipment langsung memiliki tracking number dan payment pending.</li>
                        </ul>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-slate-900">Tarif Tersedia</h3>

                        <div class="mt-4 space-y-3">
                            @forelse ($availableRates as $rate)
                                <div class="rounded-xl bg-slate-50 p-4 text-sm">
                                    <p class="font-semibold text-slate-900">
                                        {{ $rate->originBranch?->city }} -> {{ $rate->destinationBranch?->city }}
                                    </p>
                                    <p class="mt-1 text-slate-600">
                                        {{ ucfirst($rate->service_type) }} • Rp {{ number_format((float) $rate->price_per_kg, 0, ',', '.') }}/kg
                                    </p>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">Belum ada tarif tersedia.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
