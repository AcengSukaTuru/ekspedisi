@props(['value'])

@php
    $normalized = strtolower(str_replace(['_', '-'], ' ', (string) $value));

    $classes = match ($normalized) {
        'delivered', 'paid' => 'border-emerald-500/20 bg-emerald-500/10 text-emerald-400',
        'in transit', 'processed' => 'border-blue-500/20 bg-blue-500/10 text-blue-400',
        'pending' => 'border-amber-500/20 bg-amber-500/10 text-amber-400',
        'picked up' => 'border-violet-500/20 bg-violet-500/10 text-violet-400',
        default => 'border-slate-700 bg-slate-800 text-slate-300',
    };

    $label = ucwords(str_replace('_', ' ', (string) $value));
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full border px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] {$classes}"]) }}>
    {{ $label }}
</span>
