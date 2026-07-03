@props(['value'])

@php
    $normalized = strtolower(str_replace(['_', '-'], ' ', (string) $value));

    $classes = match ($normalized) {
        'delivered', 'paid', 'success', 'completed', 'active' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
        'in transit', 'picked up', 'at origin hub', 'at dest hub', 'at destination hub', 'out for delivery', 'processed' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        'pending', 'created' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        'failed delivery', 'cancelled', 'returned to sender', 'failed', 'inactive' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        default => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
    };

    $label = ucwords(str_replace('_', ' ', (string) $value));
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {$classes}"]) }}>
    {{ $label }}
</span>