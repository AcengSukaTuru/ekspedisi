<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Catat Hasil Antaran</h2>
                <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">{{ $shipment->tracking_number }}</p>
            </div>
            <a href="{{ route('courier.shipments.show', $shipment) }}" class="btn-secondary btn-sm">Kembali</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-6">
        @include('partials.flash-message')

        {{-- Info Penerima --}}
        <div class="card p-6">
            <div class="grid gap-4 md:grid-cols-2 text-sm">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">Penerima</p>
                    <p class="mt-0.5 font-bold text-slate-900 dark:text-white">{{ $shipment->receiver_name }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $shipment->receiver_phone }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">Alamat</p>
                    <p class="mt-0.5 text-sm font-medium text-slate-900 dark:text-white">{{ $shipment->receiver_address }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">Percobaan</p>
                    <p class="mt-0.5 font-bold text-slate-900 dark:text-white">#{{ $attemptNumber }} dari {{ $maxAttempts }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">Sisa</p>
                    <p class="mt-0.5 font-bold {{ $maxAttempts - $failedCount <= 1 ? 'text-red-600 dark:text-red-400' : 'text-slate-900 dark:text-white' }}">{{ max(0, $maxAttempts - $failedCount - 1) }} percobaan lagi</p>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <div class="card p-6" x-data="{ result: 'success' }">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Form Hasil Antaran</h3>

            <form method="POST" action="{{ route('courier.delivery-attempts.store', $shipment) }}" enctype="multipart/form-data" class="mt-5 grid gap-4">
                @csrf

                <div>
                    <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Hasil</label>
                    <div class="mt-2 flex gap-3">
                        <label class="flex cursor-pointer items-center gap-2.5 rounded-xl border px-5 py-3.5 text-sm transition-all" :class="result === 'success' ? 'border-emerald-500 bg-emerald-50 dark:border-emerald-500 dark:bg-emerald-900/20' : 'border-slate-200 dark:border-slate-700'">
                            <input type="radio" name="attempt_status" value="success" x-model="result" class="sr-only">
                            <div class="flex h-4 w-4 items-center justify-center rounded-full border-2" :class="result === 'success' ? 'border-emerald-500' : 'border-slate-300 dark:border-slate-600'">
                                <div class="h-2 w-2 rounded-full bg-emerald-500" x-show="result === 'success'"></div>
                            </div>
                            <span class="font-semibold text-emerald-700 dark:text-emerald-400">Berhasil Diterima</span>
                        </label>
                        <label class="flex cursor-pointer items-center gap-2.5 rounded-xl border px-5 py-3.5 text-sm transition-all" :class="result === 'failed' ? 'border-red-500 bg-red-50 dark:border-red-500 dark:bg-red-900/20' : 'border-slate-200 dark:border-slate-700'">
                            <input type="radio" name="attempt_status" value="failed" x-model="result" class="sr-only">
                            <div class="flex h-4 w-4 items-center justify-center rounded-full border-2" :class="result === 'failed' ? 'border-red-500' : 'border-slate-300 dark:border-slate-600'">
                                <div class="h-2 w-2 rounded-full bg-red-500" x-show="result === 'failed'"></div>
                            </div>
                            <span class="font-semibold text-red-700 dark:text-red-400">Gagal Dikirim</span>
                        </label>
                    </div>
                </div>

                <div x-show="result === 'failed'" x-cloak>
                    <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Alasan Gagal</label>
                    <select name="failure_reason" class="select">
                        <option value="">Pilih alasan</option>
                        @foreach ($failureReasons as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Catatan</label>
                    <textarea name="notes" rows="3" class="input" placeholder="Opsional">{{ old('notes') }}</textarea>
                </div>

                <div>
                    <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Foto Bukti</label>
                    <input name="proof_photo" type="file" accept=".jpg,.jpeg,.png" class="input">
                    <p class="mt-1 text-[10px] text-slate-400">Foto tanda terima / kondisi jika gagal. Maks 2MB.</p>
                </div>

                <div x-show="result === 'failed' && {{ $maxAttempts - $failedCount <= 1 ? 'true' : 'false' }}" x-cloak class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm dark:border-red-800 dark:bg-red-900/20">
                    <p class="font-bold text-red-800 dark:text-red-300">Perhatian!</p>
                    <p class="mt-1 text-red-700 dark:text-red-400">Ini percobaan terakhir. Jika gagal, paket otomatis dikembalikan ke pengirim (RTS).</p>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="btn-primary">Simpan Hasil</button>
                    <a href="{{ route('courier.shipments.show', $shipment) }}" class="btn-secondary">Batal</a>
                </div>
            </form>
        </div>

        {{-- Riwayat attempt sebelumnya --}}
        @if ($shipment->deliveryAttempts->count() > 0)
            <div class="card p-6">
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Percobaan Sebelumnya</h3>
                <div class="space-y-3">
                    @foreach ($shipment->deliveryAttempts as $prev)
                        <div class="rounded-xl border p-4 {{ $prev->isSuccessful() ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-900/10' : 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/10' }}">
                            <div class="flex items-center justify-between">
                                <span class="badge {{ $prev->isSuccessful() ? 'bg-emerald-200 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-red-200 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">#{{ $prev->attempt_number }} &mdash; {{ $prev->isSuccessful() ? 'Berhasil' : 'Gagal' }}</span>
                                <span class="text-xs text-slate-400">{{ optional($prev->attempted_at)->format('d M Y H:i') }}</span>
                            </div>
                            @if ($prev->isFailed())
                                <p class="mt-2 text-sm text-red-700 dark:text-red-400">Alasan: {{ $prev->failureReasonLabel() }}</p>
                            @endif
                            @if ($prev->notes)
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $prev->notes }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
