<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Kendaraan</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            @include('partials.flash-message')

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('admin.vehicles.update', $vehicle) }}" class="grid gap-4 md:grid-cols-2">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="plate_number" class="text-sm font-medium text-slate-700">Nomor Polisi</label>
                        <input id="plate_number" name="plate_number" type="text" value="{{ old('plate_number', $vehicle->plate_number) }}" class="mt-1 w-full rounded-lg border-slate-300" required>
                    </div>
                    <div>
                        <label for="vehicle_type" class="text-sm font-medium text-slate-700">Jenis Kendaraan</label>
                        <input id="vehicle_type" name="vehicle_type" type="text" value="{{ old('vehicle_type', $vehicle->vehicle_type) }}" class="mt-1 w-full rounded-lg border-slate-300" required>
                    </div>
                    <div>
                        <label for="driver_name" class="text-sm font-medium text-slate-700">Nama Driver</label>
                        <input id="driver_name" name="driver_name" type="text" value="{{ old('driver_name', $vehicle->driver_name) }}" class="mt-1 w-full rounded-lg border-slate-300" required>
                    </div>
                    <div>
                        <label for="driver_phone" class="text-sm font-medium text-slate-700">Telepon Driver</label>
                        <input id="driver_phone" name="driver_phone" type="text" value="{{ old('driver_phone', $vehicle->driver_phone) }}" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label for="status" class="text-sm font-medium text-slate-700">Status</label>
                        <select id="status" name="status" class="mt-1 w-full rounded-lg border-slate-300">
                            @foreach (['available', 'maintenance', 'on_trip'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $vehicle->status) === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-3">
                        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Update</button>
                        <a href="{{ route('admin.vehicles.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
