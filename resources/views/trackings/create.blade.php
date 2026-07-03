<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Tambah Tracking</h2>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-6">
        @include('partials.flash-message')

        <div class="card p-6">
            <div class="mb-5 rounded-xl bg-slate-50 p-4 dark:bg-slate-800/50">
                <p class="font-bold text-slate-900 dark:text-white">{{ $shipment->tracking_number }}</p>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $shipment->originBranch?->branch_name }} &rarr; {{ $shipment->destinationBranch?->branch_name }}</p>
                @if ($shipment->activeAssignment)
                    <div class="mt-2 flex gap-4 text-xs text-slate-500 dark:text-slate-400">
                        <span>Kurir: <strong class="text-slate-900 dark:text-white">{{ $shipment->activeAssignment->courier?->name }}</strong></span>
                        <span>Kendaraan: <strong class="text-slate-900 dark:text-white">{{ $shipment->activeAssignment->vehicle?->plate_number }}</strong></span>
                    </div>
                @endif
                <p class="mt-2 text-xs">Status: <span class="badge {{ $shipment->statusColor() }}">{{ $shipment->statusLabel() }}</span></p>
            </div>

            @if (count($validStatuses) === 0)
                <div class="rounded-xl bg-amber-50 p-4 text-sm text-amber-800 dark:bg-amber-900/20 dark:text-amber-300">
                    <p class="font-semibold">Tidak ada update status yang bisa dilakukan.</p>
                    <p class="mt-1">Shipment sudah dalam status akhir ({{ $shipment->statusLabel() }}).</p>
                </div>
            @else
                <form method="POST" action="{{ route('courier.trackings.store', $shipment) }}" enctype="multipart/form-data" class="grid gap-4">
                    @csrf
                    <div>
                        <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Status</label>
                        <select name="status" class="select" required>
                            @foreach ($validStatuses as $vs)
                                <option value="{{ $vs }}">{{ $statusLabels[$vs] ?? $vs }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-[10px] text-slate-400">Hanya status valid dari status saat ini.</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Lokasi</label>
                        <input name="location" type="text" value="{{ old('location', $shipment->activeAssignment->vehicle?->plate_number ?? $shipment->originBranch?->city) }}" class="input" required>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Waktu Tracking</label>
                        <input name="tracked_at" type="datetime-local" value="{{ old('tracked_at', now()->format('Y-m-d\\TH:i')) }}" class="input" required>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Keterangan</label>
                        <textarea name="description" rows="3" class="input">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-600 dark:text-slate-400">Foto Bukti (opsional)</label>
                        <input name="proof_photo" type="file" accept=".jpg,.jpeg,.png" class="input">
                        <p class="mt-1 text-[10px] text-slate-400">Maks 2MB. JPG/PNG.</p>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="btn-primary">Simpan Tracking</button>
                        <a href="{{ route('courier.shipments.show', $shipment) }}" class="btn-secondary">Batal</a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
