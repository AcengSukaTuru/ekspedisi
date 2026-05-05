<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $title }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $payment->shipment?->tracking_number }}</p>
            </div>
            <a href="{{ route($backRoute) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Kembali</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="mx-auto flex max-w-7xl flex-col gap-6">
            @include('partials.flash-message')

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
                    <h3 class="text-lg font-semibold text-slate-900">Informasi Pembayaran</h3>

                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Tracking Number</p>
                            <p class="mt-1 font-semibold text-slate-900">{{ $payment->shipment?->tracking_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Status Pembayaran</p>
                            <p class="mt-1">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase text-slate-700">{{ $payment->payment_status }}</span>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Nominal</p>
                            <p class="mt-1 font-semibold text-slate-900">Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Metode Bayar</p>
                            <p class="mt-1 font-semibold text-slate-900">{{ $payment->payment_method ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Tanggal Bayar</p>
                            <p class="mt-1 font-semibold text-slate-900">{{ optional($payment->payment_date)->format('d M Y') ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Rute</p>
                            <p class="mt-1 font-semibold text-slate-900">
                                {{ $payment->shipment?->originBranch?->branch_name }} -> {{ $payment->shipment?->destinationBranch?->branch_name }}
                            </p>
                        </div>

                        @if ($role === \App\Models\User::ROLE_ADMIN)
                            <div class="md:col-span-2">
                                <p class="text-xs uppercase tracking-wide text-slate-500">Customer</p>
                                <p class="mt-1 font-semibold text-slate-900">{{ $payment->shipment?->customer?->name ?? '-' }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-900">Bukti Pembayaran</p>
                            @if ($payment->proof_of_payment)
                                <a href="{{ asset('storage/'.$payment->proof_of_payment) }}" target="_blank" class="text-sm font-semibold text-slate-900">Lihat File</a>
                            @endif
                        </div>

                        @if ($payment->proof_of_payment)
                            @php $isPdf = str_ends_with(strtolower($payment->proof_of_payment), '.pdf'); @endphp

                            <div class="mt-3">
                                @if ($isPdf)
                                    <p class="text-sm text-slate-600">Bukti berupa PDF. Klik tombol "Lihat File" untuk membuka dokumen.</p>
                                @else
                                    <img src="{{ asset('storage/'.$payment->proof_of_payment) }}" alt="Bukti pembayaran" class="h-64 w-full rounded-xl object-cover">
                                @endif
                            </div>
                        @else
                            <p class="mt-3 text-sm text-slate-500">Belum ada bukti pembayaran yang diupload.</p>
                        @endif
                    </div>

                    <div class="mt-6">
                        <a href="{{ $shipmentRoute }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Lihat Detail Shipment</a>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-slate-900">Verifikasi Admin</h3>
                        <div class="mt-4 space-y-3 text-sm">
                            <div>
                                <p class="text-slate-500">Diverifikasi Oleh</p>
                                <p class="font-semibold text-slate-900">{{ $payment->verifier?->name ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-slate-500">Waktu Verifikasi</p>
                                <p class="font-semibold text-slate-900">{{ optional($payment->verified_at)->format('d M Y H:i') ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-slate-500">Catatan Admin</p>
                                <p class="font-semibold text-slate-900">{{ $payment->admin_note ?: '-' }}</p>
                            </div>
                        </div>
                    </div>

                    @if ($role === \App\Models\User::ROLE_CUSTOMER)
                        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-slate-900">Konfirmasi Pembayaran</h3>

                            @if ($payment->payment_status === \App\Models\Payment::STATUS_PAID)
                                <p class="mt-3 text-sm text-emerald-600">Pembayaran sudah diverifikasi paid oleh admin.</p>
                            @else
                                <form method="POST" action="{{ route('customer.payments.submit', $payment) }}" enctype="multipart/form-data" class="mt-4 grid gap-4">
                                    @csrf
                                    @method('PATCH')

                                    <div>
                                        <label for="payment_method" class="text-sm font-medium text-slate-700">Metode Pembayaran</label>
                                        <select id="payment_method" name="payment_method" class="mt-1 w-full rounded-lg border-slate-300" required>
                                            @foreach (['cash', 'transfer', 'e-wallet'] as $method)
                                                <option value="{{ $method }}" @selected(old('payment_method', $payment->payment_method) === $method)>{{ $method }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="proof_file" class="text-sm font-medium text-slate-700">Upload Bukti (transfer/e-wallet)</label>
                                        <input id="proof_file" name="proof_file" type="file" accept=".jpg,.jpeg,.png,.pdf" class="mt-1 w-full rounded-lg border-slate-300">
                                        <p class="mt-1 text-xs text-slate-500">Format: JPG, JPEG, PNG, PDF. Maksimal 2MB.</p>
                                    </div>

                                    <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Kirim Konfirmasi</button>
                                </form>
                            @endif
                        </div>
                    @endif

                    @if ($role === \App\Models\User::ROLE_ADMIN)
                        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-slate-900">Form Verifikasi Payment</h3>

                            <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" class="mt-4 grid gap-4">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <label for="payment_status" class="text-sm font-medium text-slate-700">Status Pembayaran</label>
                                    <select id="payment_status" name="payment_status" class="mt-1 w-full rounded-lg border-slate-300" required>
                                        @foreach (\App\Models\Payment::statuses() as $status)
                                            <option value="{{ $status }}" @selected(old('payment_status', $payment->payment_status) === $status)>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="admin_note" class="text-sm font-medium text-slate-700">Catatan Admin</label>
                                    <textarea id="admin_note" name="admin_note" rows="4" class="mt-1 w-full rounded-lg border-slate-300" placeholder="Opsional. Misal alasan penolakan pembayaran.">{{ old('admin_note', $payment->admin_note) }}</textarea>
                                </div>

                                <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Simpan Verifikasi</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
