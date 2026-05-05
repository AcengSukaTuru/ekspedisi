<header class="sticky top-0 z-30 border-b border-gray-200 bg-white">
    <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <button
                type="button"
                class="rounded-md border border-gray-300 p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 lg:hidden"
                @click="sidebarOpen = true"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h18"></path>
                    <path d="M3 12h18"></path>
                    <path d="M3 18h18"></path>
                </svg>
            </button>

            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">FastExpress Logistics</p>
                <h1 class="text-base font-semibold text-gray-900">{{ $pageTitle ?: 'Dashboard' }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="hidden text-right sm:block">
                <p class="text-sm font-semibold text-gray-800">{{ now()->format('d M Y') }}</p>
                <p class="text-xs text-gray-500">{{ now()->format('H:i') }} WIB</p>
            </div>

            <a href="{{ route('profile.edit') }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                Profile
            </a>
        </div>
    </div>
</header>
