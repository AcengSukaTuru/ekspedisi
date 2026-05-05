@php
    $user = auth()->user();

    $navigation = match (true) {
        $user->isAdmin() => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'match' => ['admin.dashboard']],
            ['label' => 'Shipments', 'route' => 'admin.shipments.index', 'match' => ['admin.shipments.*']],
            ['label' => 'Branches', 'route' => 'admin.branches.index', 'match' => ['admin.branches.*']],
            ['label' => 'Vehicles', 'route' => 'admin.vehicles.index', 'match' => ['admin.vehicles.*']],
            ['label' => 'Rates', 'route' => 'admin.rates.index', 'match' => ['admin.rates.*']],
            ['label' => 'Customers', 'route' => 'admin.customers.index', 'match' => ['admin.customers.*']],
            ['label' => 'Payments', 'route' => 'admin.payments.index', 'match' => ['admin.payments.*']],
        ],
        $user->isCourier() => [
            ['label' => 'Dashboard', 'route' => 'courier.dashboard', 'match' => ['courier.dashboard']],
            ['label' => 'Assigned Shipments', 'route' => 'courier.shipments.index', 'match' => ['courier.shipments.*', 'courier.trackings.*']],
        ],
        default => [
            ['label' => 'Dashboard', 'route' => 'customer.dashboard', 'match' => ['customer.dashboard']],
            ['label' => 'Create Shipment', 'route' => 'customer.shipments.create', 'match' => ['customer.shipments.create']],
            ['label' => 'My Shipments', 'route' => 'customer.shipments.index', 'match' => ['customer.shipments.index', 'customer.shipments.show', 'customer.trackings.*']],
            ['label' => 'Payments', 'route' => 'customer.payments.index', 'match' => ['customer.payments.*']],
        ],
    };
@endphp

<aside
    class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full border-r border-gray-200 bg-white transition-transform duration-200 lg:translate-x-0"
    :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
>
    <div class="flex h-full flex-col">
        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-4">
            <a href="{{ route('dashboard') }}" class="block">
                <p class="text-base font-bold text-gray-900">FastExpress</p>
                <p class="text-xs uppercase tracking-widest text-gray-500">Ekspedisi Online</p>
            </a>

            <button
                type="button"
                class="rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-800 lg:hidden"
                @click="sidebarOpen = false"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6L6 18"></path>
                    <path d="M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto p-4">
            @foreach ($navigation as $item)
                @php $active = request()->routeIs(...(array) $item['match']); @endphp
                <a
                    href="{{ route($item['route']) }}"
                    class="block rounded-lg px-3 py-2 text-sm font-medium transition {{ $active ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="border-t border-gray-200 p-4">
            <p class="truncate text-sm font-semibold text-gray-900">{{ $user->name }}</p>
            <p class="truncate text-xs uppercase tracking-wider text-gray-500">{{ $user->role }}</p>

            <div class="mt-3 flex gap-2">
                <a href="{{ route('profile.edit') }}" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-center text-xs font-semibold text-gray-700 hover:bg-gray-50">
                    Profile
                </a>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
