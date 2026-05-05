<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Customer</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @include('partials.flash-message')

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-slate-900">Daftar Customer</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-slate-600">Nama</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-600">Email</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-600">Telepon</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-600">Alamat</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-600">Akun</th>
                                <th class="px-6 py-3 text-left font-semibold text-slate-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($customers as $customer)
                                <tr>
                                    <td class="px-6 py-4 font-medium text-slate-900">{{ $customer->name }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $customer->email }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $customer->phone }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $customer->address }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $customer->user?->email ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-3">
                                            <a href="{{ route('admin.customers.edit', $customer) }}" class="font-semibold text-slate-900">Edit</a>
                                            <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-semibold text-red-600" onclick="return confirm('Hapus customer ini?')">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-6 text-center text-slate-500">Belum ada data customer.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
