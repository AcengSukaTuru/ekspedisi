<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Cabang</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            @include('partials.flash-message')

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('admin.branches.update', $branch) }}" class="grid gap-4 md:grid-cols-2">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="branch_name" class="text-sm font-medium text-slate-700">Nama Cabang</label>
                        <input id="branch_name" name="branch_name" type="text" value="{{ old('branch_name', $branch->branch_name) }}" class="mt-1 w-full rounded-lg border-slate-300" required>
                    </div>
                    <div>
                        <label for="city" class="text-sm font-medium text-slate-700">Kota</label>
                        <input id="city" name="city" type="text" value="{{ old('city', $branch->city) }}" class="mt-1 w-full rounded-lg border-slate-300" required>
                    </div>
                    <div class="md:col-span-2">
                        <label for="address" class="text-sm font-medium text-slate-700">Alamat</label>
                        <textarea id="address" name="address" rows="3" class="mt-1 w-full rounded-lg border-slate-300" required>{{ old('address', $branch->address) }}</textarea>
                    </div>
                    <div>
                        <label for="phone" class="text-sm font-medium text-slate-700">Telepon</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone', $branch->phone) }}" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div class="flex items-end gap-3">
                        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Update</button>
                        <a href="{{ route('admin.branches.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
