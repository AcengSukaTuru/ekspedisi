@php
    $user = auth()->user();

    $navigation = match (true) {
        $user->isAdmin() => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'grid'],
            ['label' => 'Shipments', 'route' => 'admin.shipments.index', 'match' => ['admin.shipments.*'], 'icon' => 'package'],
            ['label' => 'Payments', 'route' => 'admin.payments.index', 'match' => ['admin.payments.*'], 'icon' => 'credit-card'],
            ['divider' => 'Master Data'],
            ['label' => 'Branches', 'route' => 'admin.branches.index', 'match' => ['admin.branches.*'], 'icon' => 'building'],
            ['label' => 'Vehicles', 'route' => 'admin.vehicles.index', 'match' => ['admin.vehicles.*'], 'icon' => 'truck'],
            ['label' => 'Rates', 'route' => 'admin.rates.index', 'match' => ['admin.rates.*'], 'icon' => 'tag'],
            ['label' => 'Customers', 'route' => 'admin.customers.index', 'match' => ['admin.customers.*'], 'icon' => 'users'],
        ],
        $user->isCourier() => [
            ['label' => 'Dashboard', 'route' => 'courier.dashboard', 'icon' => 'grid'],
            ['label' => 'My Shipments', 'route' => 'courier.shipments.index', 'match' => ['courier.shipments.*', 'courier.trackings.*', 'courier.delivery-attempts.*'], 'icon' => 'package'],
        ],
        default => [
            ['label' => 'Dashboard', 'route' => 'customer.dashboard', 'icon' => 'grid'],
            ['label' => 'New Shipment', 'route' => 'customer.shipments.create', 'icon' => 'plus-circle'],
            ['label' => 'My Shipments', 'route' => 'customer.shipments.index', 'match' => ['customer.shipments.*', 'customer.trackings.*'], 'icon' => 'package'],
            ['label' => 'Payments', 'route' => 'customer.payments.index', 'match' => ['customer.payments.*'], 'icon' => 'credit-card'],
        ],
    };

    $icons = [
        'grid' => '<path d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z"/>',
        'package' => '<path d="M16.5 9.4l-9-5.19M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>',
        'credit-card' => '<rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>',
        'building' => '<path d="M6 22V4a2 2 0 012-2h8a2 2 0 012 2v18z"/><path d="M6 12H4a2 2 0 00-2 2v6a2 2 0 002 2h2"/><path d="M18 9h2a2 2 0 012 2v9a2 2 0 01-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/>',
        'truck' => '<path d="M1 3h15v13H1z"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
        'tag' => '<path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>',
        'users' => '<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>',
        'plus-circle' => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>',
    ];
@endphp

<aside
    class="fixed inset-y-0 left-0 z-50 w-72 -translate-x-full border-r border-slate-200 bg-white transition-transform duration-300 lg:translate-x-0 dark:border-slate-800 dark:bg-slate-900"
    :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
>
    <div class="flex h-full flex-col">
        {{-- Brand --}}
        <div class="flex h-16 items-center gap-3 border-b border-slate-200 px-5 dark:border-slate-800">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 shadow-lg shadow-blue-600/20">
                <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16.5 9.4l-9-5.19M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-900 dark:text-white">FastExpress</p>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-blue-600 dark:text-blue-400">Logistics</p>
            </div>
            <button
                type="button"
                class="ml-auto rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 lg:hidden dark:hover:bg-slate-800 dark:hover:text-slate-300"
                @click="sidebarOpen = false"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 space-y-1 overflow-y-auto p-4">
            @foreach ($navigation as $item)
                @if (isset($item['divider']))
                    <p class="mb-1 mt-4 px-3.5 text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-600">{{ $item['divider'] }}</p>
                    @continue
                @endif

                @php
                    $matchRoutes = $item['match'] ?? [$item['route']];
                    $active = request()->routeIs(...$matchRoutes);
                @endphp

                <a
                    href="{{ route($item['route']) }}"
                    class="{{ $active ? 'sidebar-link-active' : 'sidebar-link' }}"
                >
                    <svg class="h-[18px] w-[18px] flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        {!! $icons[$item['icon']] ?? $icons['grid'] !!}
                    </svg>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- User Card --}}
        <div class="border-t border-slate-200 p-4 dark:border-slate-800">
            <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3 dark:bg-slate-800/50">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-indigo-500 text-sm font-bold text-white">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $user->name }}</p>
                    <p class="text-[11px] font-medium uppercase tracking-wide text-blue-600 dark:text-blue-400">{{ $user->role }}</p>
                </div>
            </div>

            <div class="mt-3 flex gap-2">
                <a href="{{ route('profile.edit') }}" class="btn-secondary btn-sm flex-1 text-center">Profile</a>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="btn-danger btn-sm w-full">Logout</button>
                </form>
            </div>
        </div>
    </div>
</aside>
