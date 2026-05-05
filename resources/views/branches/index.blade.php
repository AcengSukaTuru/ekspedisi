<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Cabang</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            @include('partials.flash-message')

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">Tambah Cabang</h3>
                <form method="POST" action="{{ route('admin.branches.store') }}" class="mt-4 grid gap-4 md:grid-cols-2">
                    @csrf
                    <div>
                        <label for="branch_name" class="text-sm font-medium text-slate-700">Nama Cabang</label>
                        <input id="branch_name" name="branch_name" type="text" value="{{ old('branch_name') }}" class="mt-1 w-full rounded-lg border-slate-300" required>
                    </div>
                    <div>
                        <label for="city" class="text-sm font-medium text-slate-700">Kota</label>
                        <input id="city" name="city" type="text" value="{{ old('city') }}" class="mt-1 w-full rounded-lg border-slate-300" required>
                    </div>
                    <div class="md:col-span-2">
                        <label for="address" class="text-sm font-medium text-slate-700">Alamat</label>
                        <textarea id="address" name="address" rows="3" class="mt-1 w-full rounded-lg border-slate-300" required>{{ old('address') }}</textarea>
                    </div>
                    <div>
                        <label for="phone" class="text-sm font-medium text-slate-700">Telepon</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone') }}" class="mt-1 w-full rounded-lg border-slate-300">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Simpan Cabang</button>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-slate-900">Daftar Cabang</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-slate-600">Nama</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-600">Kota</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-600">Alamat</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-600">Telepon</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($branches as $branch)
                                <tr>
                                    <td class="px-6 py-4 font-medium text-slate-900">{{ $branch->branch_name }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $branch->city }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $branch->address }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $branch->phone ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-3">
                                            <a href="{{ route('admin.branches.edit', $branch) }}" class="font-semibold text-slate-900">Edit</a>
                                            <form method="POST" action="{{ route('admin.branches.destroy', $branch) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-semibold text-red-600" onclick="return confirm('Hapus cabang ini?')">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-6 text-center text-slate-500">Belum ada data cabang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4">
                    {{ $branches->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
