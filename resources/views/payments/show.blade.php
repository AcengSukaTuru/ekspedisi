<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $title }}</h2>
                <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">{{ $payment->shipment?->tracking_number }}</p>
            </div>
            <a href="{{ route($backRoute) }}" class="btn-secondary btn-sm">Kembali</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        @include('partials.flash-message')

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="card p-6 lg:col-span-2">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Informasi Pembayaran</h3>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    @php
                        $fields = [
                            ['label' => 'Tracking', 'value' => $payment->shipment?->tracking_number],
                            ['label' => 'Status', 'html' => '<span class="badge '.match($payment->payment_status) { 'paid' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400', 'failed' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', default => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }.'">'.$payment->statusLabel().'</span>'],
                            ['label' => 'Tipe', 'html' => '<span class="badge '.($payment->isCod() ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400').'">'.($payment->isCod() ? 'COD' : 'Prepaid').'</span>'],
                            ['label' => 'Nominal', 'value' => 'Rp '.number_format((float) $payment->amount, 0, ',', '.')],
                            ['label' => 'Metode', 'value' => $payment->payment_method ?? '-'],
                            ['label' => 'Tanggal Bayar', 'value' => optional($payment->payment_date)->format('d M Y') ?? '-'],
                            ['label' => 'Rute', 'value' => $payment->shipment?->originBranch?->branch_name.' &rarr; '.$payment->shipment?->destinationBranch?->branch_name],
                        ];
                        if ($role === \App\Models\User::ROLE_ADMIN) {
                            $fields[] = ['label' => 'Customer', 'value' => $payment->shipment?->customer?->name ?? '-'];
                        }
                    @endphp
                    @foreach ($fields as $f)
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">{{ $f['label'] }}</p>
                            <p class="mt-0.5 text-sm font-semibold text-slate-900 dark:text-white">{!! $f['html'] ?? e($f['value']) !!}</p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 rounded-xl bg-slate-50 p-4 dark:bg-slate-800/50">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-bold text-slate-900 dark:text-white">Bukti Pembayaran</p>
                        @if ($payment->proof_of_payment)
                            <a href="{{ asset('storage/'.$payment->proof_of_payment) }}" target="_blank" class="text-sm font-semibold text-blue-600 dark:text-blue-400">Lihat File</a>
                        @endif
                    </div>
                    @if ($payment->proof_of_payment)
                        @php $isPdf = str_ends_with(strtolower($payment->proof_of_payment), '.pdf'); @endphp
                        <div class="mt-3">
                            @if ($isPdf)
                                <p class="text-sm text-slate-500 dark:text-slate-400">Bukti berupa PDF. Klik "Lihat File" untuk membuka.</p>
                            @else
                                <img src="{{ asset('storage/'.$payment->proof_of_payment) }}" alt="Bukti pembayaran" class="h-64 w-full rounded-xl object-cover">
                            @endif
                        </div>
                    @else
                        <p class="mt-2 text-sm text-slate-400">Belum ada bukti pembayaran.</p>
                    @endif
                </div>

                <div class="mt-4">
                    <a href="{{ $shipmentRoute }}" class="btn-secondary btn-sm">Lihat Detail Shipment</a>
                </div>
            </div>

            <div class="space-y-6">
                <div class="card p-6">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Verifikasi</h3>
                    <div class="mt-4 space-y-3 text-sm">
                        <div>
                            <p class="text-slate-500 dark:text-slate-400">Diverifikasi Oleh</p>
                            <p class="font-semibold text-slate-900 dark:text-white">{{ $payment->verifier?->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500 dark:text-slate-400">Waktu</p>
                            <p class="font-semibold text-slate-900 dark:text-white">{{ optional($payment->verified_at)->format('d M Y H:i') ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500 dark:text-slate-400">Catatan</p>
                            <p class="font-semibold text-slate-900 dark:text-white">{{ $payment->admin_note ?: '-' }}</p>
                        </div>
                    </div>
                </div>

                @if ($payment->isCod() && $payment->isCodCollected())
                    <div class="card p-6">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">COD Collection</h3>
                        <div class="mt-3 space-y-2 text-sm">
                            <div>
                                <p class="text-slate-500 dark:text-slate-400">Dikumpulkan Oleh</p>
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $payment->collectedBy?->name }}</p>
                            </div>
                            <div>
                                <p class="text-slate-500 dark:text-slate-400">Waktu</p>
                                <p class="font-semibold text-slate-900 dark:text-white">{{ optional($payment->cod_collected_at)->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($role === \App\Models\User::ROLE_CUSTOMER)
                    <div class="card p-6">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Konfirmasi Pembayaran</h3>
                        @if ($payment->payment_status === \App\Models\Payment::STATUS_PAID)
                            <p class="mt-3 text-sm text-emerald-600 dark:text-emerald-400">Pembayaran sudah diverifikasi lunas.</p>
                        @else
                            <form method="POST" action="{{ route('customer.payments.submit', $payment) }}" enctype="multipart/form-data" class="mt-4 grid gap-4">
                                @csrf @method('PATCH')
                                <div>
                                    <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Metode</label>
                                    <select name="payment_method" class="select" required>
                                        @foreach (['cash', 'transfer', 'e-wallet'] as $m)
                                            <option value="{{ $m }}" @selected(old('payment_method', $payment->payment_method) === $m)>{{ ucfirst($m) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Upload Bukti (transfer/e-wallet)</label>
                                    <input name="proof_file" type="file" accept=".jpg,.jpeg,.png,.pdf" class="input">
                                    <p class="mt-1 text-[10px] text-slate-400">JPG, PNG, PDF. Maks 2MB.</p>
                                </div>
                                <button type="submit" class="btn-primary">Kirim Konfirmasi</button>
                            </form>
                        @endif
                    </div>
                @endif

                @if ($role === \App\Models\User::ROLE_ADMIN)
                    <div class="card p-6">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Form Verifikasi</h3>
                        <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" class="mt-4 grid gap-4">
                            @csrf @method('PATCH')
                            <div>
                                <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Status</label>
                                <select name="payment_status" class="select" required>
                                    @foreach (\App\Models\Payment::statuses() as $s)
                                        <option value="{{ $s }}" @selected(old('payment_status', $payment->payment_status) === $s)>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Catatan</label>
                                <textarea name="admin_note" rows="3" class="input" placeholder="Opsional">{{ old('admin_note', $payment->admin_note) }}</textarea>
                            </div>
                            <button type="submit" class="btn-primary">Simpan Verifikasi</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
