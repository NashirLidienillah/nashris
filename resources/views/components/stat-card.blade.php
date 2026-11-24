@props([
    'title' => '',
    'value' => '0',
    'icon' => null,
    'color' => 'indigo', // indigo, emerald, amber, rose, sky
    'subtitle' => null
])

@php
    $colorClasses = [
        'indigo' => 'bg-indigo-50 border-indigo-200 text-indigo-600',
        'emerald' => 'bg-emerald-50 border-emerald-200 text-emerald-600',
        'amber' => 'bg-amber-50 border-amber-200 text-amber-600',
        'rose' => 'bg-rose-50 border-rose-200 text-rose-600',
        'sky' => 'bg-sky-50 border-sky-200 text-sky-600',
    ][$color] ?? 'bg-indigo-50 border-indigo-200 text-indigo-600';
@endphp

<div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-between transition hover:shadow-sm">
    <div>
        <span class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">{{ $title }}</span>
        <div class="text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $value }}</div>
        @if($subtitle)
            <span class="block mt-1 text-xs text-slate-500 font-medium">{{ $subtitle }}</span>
        @endif
    </div>

    @if($icon)
        <div class="w-12 h-12 rounded-2xl border {{ $colorClasses }} flex items-center justify-center text-xl flex-shrink-0 shadow-xs">
            {{ $icon }}
        </div>
    @endif
</div>
