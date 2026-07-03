<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-xl font-semibold text-xs text-white shadow-lg shadow-blue-600/20 hover:bg-blue-500 active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-blue-500/30 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>