<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Tarif</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            @include('partials.flash-message')

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('admin.rates.update', $rate) }}" class="grid gap-4 md:grid-cols-2">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="origin_branch_id" class="text-sm font-medium text-slate-700">Cabang Asal</label>
                        <select id="origin_branch_id" name="origin_branch_id" class="mt-1 w-full rounded-lg border-slate-300" required>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected(old('origin_branch_id', $rate->origin_branch_id) == $branch->id)>{{ $branch->branch_name }} - {{ $branch->city }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="destination_branch_id" class="text-sm font-medium text-slate-700">Cabang Tujuan</label>
                        <select id="destination_branch_id" name="destination_branch_id" class="mt-1 w-full rounded-lg border-slate-300" required>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected(old('destination_branch_id', $rate->destination_branch_id) == $branch->id)>{{ $branch->branch_name }} - {{ $branch->city }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="price_per_kg" class="text-sm font-medium text-slate-700">Harga per Kg</label>
                        <input id="price_per_kg" name="price_per_kg" type="number" min="1" step="0.01" value="{{ old('price_per_kg', $rate->price_per_kg) }}" class="mt-1 w-full rounded-lg border-slate-300" required>
                    </div>
                    <div>
                        <label for="service_type" class="text-sm font-medium text-slate-700">Jenis Layanan</label>
                        <select id="service_type" name="service_type" class="mt-1 w-full rounded-lg border-slate-300" required>
                            @foreach (['regular', 'express'] as $service)
                                <option value="{{ $service }}" @selected(old('service_type', $rate->service_type) === $service)>{{ $service }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-3">
                        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Update</button>
                        <a href="{{ route('admin.rates.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
