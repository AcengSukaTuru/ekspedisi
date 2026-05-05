<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Kendaraan</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            @include('partials.flash-message')

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">Tambah Kendaraan</h3>
                <form method="POST" action="{{ route('admin.vehicles.store') }}" class="mt-4 grid gap-4 md:grid-cols-2">
                    @csrf
                    <div>
                        <label for="plate_number" class="text-sm font-medium text-slate-700">Nomor Polisi</label>
                        <input id="plate_number" name="plate_number" type="text" value="{{ old('plate_number') }}" class="mt-1 w-full rounded-lg border-slate-300" required>
                    </div>
                    <div>
                        <label for="vehicle_type" class="text-sm font-medium text-slate-700">Jenis Kendaraan</label>
                        <input id="vehicle_type" name="vehicle_type" type="text" value="{{ old('vehicle_type') }}" class="mt-1 w-full rounded-lg border-slate-300" required>
                    </div>
                    <div>
                        <label for="driver_name" class="text-sm font-medium text-slate-700">Nama Driver</label>
                        <input id="driver_name" name="driver_name" type="text" value="{{ old('driver_name') }}" class="mt-1 w-full rounded-lg border-slate-300" required>
                    </div>
                    <div>
                        <label for="driver_phone" class="text-sm font-medium text-slate-700">Telepon Driver</label>
                        <input id="driver_phone" name="driver_phone" type="text" value="{{ old('driver_phone') }}" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div>
                        <label for="status" class="text-sm font-medium text-slate-700">Status</label>
                        <select id="status" name="status" class="mt-1 w-full rounded-lg border-slate-300">
                            <option value="available">available</option>
                            <option value="maintenance">maintenance</option>
                            <option value="on_trip">on_trip</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Simpan Kendaraan</button>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-slate-900">Daftar Kendaraan</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-slate-600">Plat</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-600">Jenis</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-600">Driver</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-600">Telepon</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-600">Status</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($vehicles as $vehicle)
                                <tr>
                                    <td class="px-6 py-4 font-medium text-slate-900">{{ $vehicle->plate_number }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $vehicle->vehicle_type }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $vehicle->driver_name }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $vehicle->driver_phone ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase text-slate-700">{{ $vehicle->status }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-3">
                                            <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="font-semibold text-slate-900">Edit</a>
                                            <form method="POST" action="{{ route('admin.vehicles.destroy', $vehicle) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-semibold text-red-600" onclick="return confirm('Hapus kendaraan ini?')">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-6 text-center text-slate-500">Belum ada data kendaraan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4">
                    {{ $vehicles->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
