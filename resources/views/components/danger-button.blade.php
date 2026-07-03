<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-xl font-semibold text-xs text-white shadow-lg shadow-red-600/20 hover:bg-red-500 active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-red-500/30 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>