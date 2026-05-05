<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Tarif</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            @include('partials.flash-message')

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">Tambah Tarif</h3>
                <form method="POST" action="{{ route('admin.rates.store') }}" class="mt-4 grid gap-4 md:grid-cols-2">
                    @csrf
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
                        <label for="price_per_kg" class="text-sm font-medium text-slate-700">Harga per Kg</label>
                        <input id="price_per_kg" name="price_per_kg" type="number" min="1" step="0.01" value="{{ old('price_per_kg') }}" class="mt-1 w-full rounded-lg border-slate-300" required>
                    </div>
                    <div>
                        <label for="service_type" class="text-sm font-medium text-slate-700">Jenis Layanan</label>
                        <select id="service_type" name="service_type" class="mt-1 w-full rounded-lg border-slate-300" required>
                            @foreach (['regular', 'express'] as $service)
                                <option value="{{ $service }}" @selected(old('service_type') === $service)>{{ $service }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Simpan Tarif</button>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-slate-900">Daftar Tarif</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-slate-600">Asal</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-600">Tujuan</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-600">Layanan</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-600">Harga/Kg</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($rates as $rate)
                                <tr>
                                    <td class="px-6 py-4 text-slate-700">{{ $rate->originBranch?->branch_name }}</td>
                                    <td class="px-6 py-4 text-slate-700">{{ $rate->destinationBranch?->branch_name }}</td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase text-slate-700">{{ $rate->service_type }}</span>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-slate-900">Rp {{ number_format((float) $rate->price_per_kg, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-3">
                                            <a href="{{ route('admin.rates.edit', $rate) }}" class="font-semibold text-slate-900">Edit</a>
                                            <form method="POST" action="{{ route('admin.rates.destroy', $rate) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-semibold text-red-600" onclick="return confirm('Hapus tarif ini?')">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-6 text-center text-slate-500">Belum ada data tarif.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4">
                    {{ $rates->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
