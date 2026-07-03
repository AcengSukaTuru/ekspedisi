<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Buat Shipment Baru</h2>
    </x-slot>

    <div class="mx-auto max-w-5xl space-y-6">
        @include('partials.flash-message')

        <div class="grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
            <div class="card p-6">
                <form method="POST" action="{{ route('customer.shipments.store') }}" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2" x-data="{ pickupType: 'drop_off', paymentType: 'prepaid' }">
                    @csrf
                    <div class="md:col-span-2 rounded-xl bg-blue-50 p-4 text-sm text-blue-800 dark:bg-blue-900/20 dark:text-blue-300">
                        <p class="font-semibold">Alur otomatis saat shipment dibuat</p>
                        <p class="mt-1">Nomor tracking unik, ongkir dari tarif cabang, data pembayaran, dan tracking awal dibuat otomatis.</p>
                    </div>

                    {{-- Pickup Type --}}
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Cara Pengiriman</label>
                        <div class="mt-2 flex gap-3">
                            @foreach ([['drop_off', 'Drop-off (Antar ke Cabang)'], ['pickup_request', 'Request Pickup (Kurir Jemput)']] as [$val, $lbl])
                                <label class="flex cursor-pointer items-center gap-2.5 rounded-xl border px-4 py-3 text-sm transition-all" :class="pickupType === '{{ $val }}' ? 'border-blue-500 bg-blue-50 dark:border-blue-500 dark:bg-blue-900/20' : 'border-slate-200 dark:border-slate-700'">
                                    <input type="radio" name="pickup_type" value="{{ $val }}" x-model="pickupType" class="sr-only">
                                    <div class="flex h-4 w-4 items-center justify-center rounded-full border-2 transition-all" :class="pickupType === '{{ $val }}' ? 'border-blue-500' : 'border-slate-300 dark:border-slate-600'">
                                        <div class="h-2 w-2 rounded-full bg-blue-500 transition-all" x-show="pickupType === '{{ $val }}'"></div>
                                    </div>
                                    <span class="text-slate-700 dark:text-slate-300">{{ $lbl }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Pickup address --}}
                    <div x-show="pickupType === 'pickup_request'" x-cloak class="md:col-span-2 grid gap-3 md:grid-cols-2 rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                        <div class="md:col-span-2"><p class="text-sm font-bold text-blue-800 dark:text-blue-300">Info Penjemputan</p></div>
                        <div class="md:col-span-2"><label class="text-xs font-medium text-slate-600 dark:text-slate-400">Alamat</label><textarea name="pickup_address" rows="2" class="input" placeholder="Alamat lengkap penjemputan">{{ old('pickup_address') }}</textarea></div>
                        <div><label class="text-xs font-medium text-slate-600 dark:text-slate-400">Nama Kontak</label><input name="pickup_contact_name" type="text" value="{{ old('pickup_contact_name', auth()->user()->name) }}" class="input"></div>
                        <div><label class="text-xs font-medium text-slate-600 dark:text-slate-400">Telepon</label><input name="pickup_contact_phone" type="text" value="{{ old('pickup_contact_phone', auth()->user()->customer?->phone) }}" class="input"></div>
                    </div>

                    {{-- Rute & Layanan --}}
                    @foreach ([['origin_branch_id', 'Cabang Asal', $branches], ['destination_branch_id', 'Cabang Tujuan', $branches]] as [$name, $label, $opts])
                        <div>
                            <label for="{{ $name }}" class="text-xs font-medium text-slate-600 dark:text-slate-400">{{ $label }}</label>
                            <select id="{{ $name }}" name="{{ $name }}" class="select" required>
                                <option value="">Pilih</option>
                                @foreach ($opts as $b)
                                    <option value="{{ $b->id }}" @selected(old($name) == $b->id)>{{ $b->branch_name }} - {{ $b->city }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                    <div>
                        <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Jenis Layanan</label>
                        <select name="service_type" class="select" required>
                            @foreach ($serviceTypes as $st)<option value="{{ $st }}" @selected(old('service_type') === $st)>{{ ucfirst($st) }}</option>@endforeach
                        </select>
                    </div>
                    <div><label class="text-xs font-medium text-slate-600 dark:text-slate-400">Total Berat (Kg)</label><input name="total_weight" type="number" min="0.1" step="0.01" value="{{ old('total_weight') }}" class="input" required></div>
                    <div><label class="text-xs font-medium text-slate-600 dark:text-slate-400">Tanggal Kirim</label><input name="shipment_date" type="date" value="{{ old('shipment_date', now()->toDateString()) }}" class="input"></div>
                    <div><label class="text-xs font-medium text-slate-600 dark:text-slate-400">Estimasi Tiba</label><input name="estimated_arrival" type="date" value="{{ old('estimated_arrival', now()->addDays(3)->toDateString()) }}" class="input"></div>

                    {{-- Pengirim --}}
                    <div><label class="text-xs font-medium text-slate-600 dark:text-slate-400">Nama Pengirim</label><input name="sender_name" type="text" value="{{ old('sender_name', auth()->user()->name) }}" class="input" required></div>
                    <div><label class="text-xs font-medium text-slate-600 dark:text-slate-400">Telepon Pengirim</label><input name="sender_phone" type="text" value="{{ old('sender_phone', auth()->user()->customer?->phone) }}" class="input" required></div>
                    <div class="md:col-span-2"><label class="text-xs font-medium text-slate-600 dark:text-slate-400">Alamat Pengirim</label><textarea name="sender_address" rows="2" class="input" required>{{ old('sender_address', auth()->user()->customer?->address) }}</textarea></div>

                    {{-- Penerima --}}
                    <div><label class="text-xs font-medium text-slate-600 dark:text-slate-400">Nama Penerima</label><input name="receiver_name" type="text" value="{{ old('receiver_name') }}" class="input" required></div>
                    <div><label class="text-xs font-medium text-slate-600 dark:text-slate-400">Telepon Penerima</label><input name="receiver_phone" type="text" value="{{ old('receiver_phone') }}" class="input" required></div>
                    <div class="md:col-span-2"><label class="text-xs font-medium text-slate-600 dark:text-slate-400">Alamat Penerima</label><textarea name="receiver_address" rows="2" class="input" required>{{ old('receiver_address') }}</textarea></div>

                    {{-- Barang --}}
                    <div><label class="text-xs font-medium text-slate-600 dark:text-slate-400">Nama Barang</label><input name="item_name" type="text" value="{{ old('item_name') }}" class="input" required></div>
                    <div><label class="text-xs font-medium text-slate-600 dark:text-slate-400">Jumlah</label><input name="quantity" type="number" min="1" value="{{ old('quantity', 1) }}" class="input" required></div>
                    <div class="md:col-span-2"><label class="text-xs font-medium text-slate-600 dark:text-slate-400">Deskripsi</label><textarea name="description" rows="2" class="input">{{ old('description') }}</textarea></div>
                    <div class="md:col-span-2"><label class="text-xs font-medium text-slate-600 dark:text-slate-400">Foto Barang</label><input name="photo" type="file" class="input"><p class="mt-1 text-[10px] text-slate-400">Opsional. Maks 2 MB.</p></div>

                    {{-- Payment Type --}}
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Tipe Pembayaran</label>
                        <div class="mt-2 flex gap-3">
                            @foreach ([['prepaid', 'Prepaid (Bayar di Depan)'], ['cod', 'COD (Bayar di Tempat)']] as [$val, $lbl])
                                <label class="flex cursor-pointer items-center gap-2.5 rounded-xl border px-4 py-3 text-sm transition-all" :class="paymentType === '{{ $val }}' ? 'border-blue-500 bg-blue-50 dark:border-blue-500 dark:bg-blue-900/20' : 'border-slate-200 dark:border-slate-700'">
                                    <input type="radio" name="payment_type" value="{{ $val }}" x-model="paymentType" class="sr-only">
                                    <div class="flex h-4 w-4 items-center justify-center rounded-full border-2" :class="paymentType === '{{ $val }}' ? 'border-blue-500' : 'border-slate-300 dark:border-slate-600'">
                                        <div class="h-2 w-2 rounded-full bg-blue-500" x-show="paymentType === '{{ $val }}'"></div>
                                    </div>
                                    <span class="text-slate-700 dark:text-slate-300">{{ $lbl }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p x-show="paymentType === 'cod'" x-cloak class="mt-2 text-xs text-orange-600 dark:text-orange-400">COD: Penerima membayar ongkir ke kurir saat menerima paket.</p>
                    </div>

                    <div class="md:col-span-2 flex items-center justify-between rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-5 py-4 text-sm text-white">
                        <span>Ongkir dihitung otomatis: tarif/kg x berat.</span>
                        <button type="submit" class="rounded-lg bg-white px-5 py-2.5 font-bold text-blue-700 shadow-lg hover:bg-blue-50 transition">Simpan Shipment</button>
                    </div>
                </form>
            </div>

            <div class="space-y-6">
                <div class="card p-6">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Panduan</h3>
                    <ul class="mt-3 space-y-2 text-sm text-slate-600 dark:text-slate-400">
                        <li class="flex items-start gap-2"><svg class="mt-0.5 h-4 w-4 text-blue-500 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Cabang asal dan tujuan harus berbeda.</li>
                        <li class="flex items-start gap-2"><svg class="mt-0.5 h-4 w-4 text-blue-500 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Pilih regular atau express sesuai tarif.</li>
                        <li class="flex items-start gap-2"><svg class="mt-0.5 h-4 w-4 text-blue-500 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Drop-off: antar ke cabang. Pickup: kurir jemput.</li>
                        <li class="flex items-start gap-2"><svg class="mt-0.5 h-4 w-4 text-blue-500 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Prepaid: bayar di depan. COD: penerima bayar.</li>
                    </ul>
                </div>
                <div class="card p-6">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Tarif Tersedia</h3>
                    <div class="mt-3 space-y-2">
                        @forelse ($availableRates as $rate)
                            <div class="flex items-center justify-between rounded-xl bg-slate-50 p-3 dark:bg-slate-800/50">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $rate->originBranch?->city }} &rarr; {{ $rate->destinationBranch?->city }}</p>
                                    <p class="text-[10px] uppercase text-slate-400">{{ $rate->service_type }}</p>
                                </div>
                                <p class="text-sm font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format((float) $rate->price_per_kg, 0, ',', '.') }}/kg</p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-400">Belum ada tarif.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
