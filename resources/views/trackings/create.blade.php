<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Tracking Shipment</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            @include('partials.flash-message')

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-4 rounded-xl bg-slate-50 p-4 text-sm text-slate-600">
                    <p class="font-semibold text-slate-900">{{ $shipment->tracking_number }}</p>
                    <p>{{ $shipment->originBranch?->branch_name }} -> {{ $shipment->destinationBranch?->branch_name }}</p>
                </div>

                <form method="POST" action="{{ route('courier.trackings.store', $shipment) }}" class="grid gap-4">
                    @csrf
                    <div>
                        <label for="status" class="text-sm font-medium text-slate-700">Status</label>
                        <select id="status" name="status" class="mt-1 w-full rounded-lg border-slate-300" required>
                            @foreach (['picked_up', 'in_transit', 'delivered'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $shipment->status) === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="location" class="text-sm font-medium text-slate-700">Lokasi</label>
                        <input id="location" name="location" type="text" value="{{ old('location', $shipment->vehicle?->plate_number) }}" class="mt-1 w-full rounded-lg border-slate-300" required>
                    </div>
                    <div>
                        <label for="tracked_at" class="text-sm font-medium text-slate-700">Waktu Tracking</label>
                        <input id="tracked_at" name="tracked_at" type="datetime-local" value="{{ old('tracked_at', now()->format('Y-m-d\\TH:i')) }}" class="mt-1 w-full rounded-lg border-slate-300" required>
                    </div>
                    <div>
                        <label for="description" class="text-sm font-medium text-slate-700">Keterangan</label>
                        <textarea id="description" name="description" rows="3" class="mt-1 w-full rounded-lg border-slate-300">{{ old('description') }}</textarea>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Simpan Tracking</button>
                        <a href="{{ route('courier.shipments.show', $shipment) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
