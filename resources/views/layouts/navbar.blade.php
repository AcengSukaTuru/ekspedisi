<header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/80 backdrop-blur-xl dark:border-slate-800/80 dark:bg-slate-950/80 transition-colors duration-300">
    <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <button
                type="button"
                class="rounded-xl border border-slate-200 p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700 lg:hidden dark:border-slate-700 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 transition-colors"
                @click="sidebarOpen = true"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h18M3 12h18M3 18h18"/>
                </svg>
            </button>

            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-blue-600 dark:text-blue-400">FastExpress</p>
                <h1 class="text-base font-bold text-slate-900 dark:text-white">{{ $pageTitle ?: 'Dashboard' }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-2">
            {{-- Date --}}
            <div class="hidden text-right sm:block mr-2">
                <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">{{ now()->format('d M Y') }}</p>
                <p class="text-[10px] text-slate-400 dark:text-slate-500">{{ now()->format('H:i') }} WIB</p>
            </div>

            {{-- Dark Mode Toggle --}}
            <button
                @click="toggle()"
                class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 text-slate-500 transition-all hover:bg-slate-100 hover:text-slate-700 dark:border-slate-700 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200"
                title="Toggle dark mode"
            >
                {{-- Sun icon (shown in dark mode) --}}
                <svg x-show="dark" x-cloak class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                </svg>
                {{-- Moon icon (shown in light mode) --}}
                <svg x-show="!dark" x-cloak class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                </svg>
            </button>

            {{-- Profile --}}
            <a href="{{ route('profile.edit') }}" class="btn-secondary btn-sm hidden sm:inline-flex">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Profile
            </a>
        </div>
    </div>
</header>
