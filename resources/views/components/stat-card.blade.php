@props([
    'title' => '',
    'value' => '0',
    'icon' => null,
    'color' => 'indigo', // indigo, emerald, amber, rose, sky
    'subtitle' => null
])

@php
    $colorClasses = [
        'indigo' => 'bg-indigo-50 border-indigo-200/80 text-indigo-600',
        'emerald' => 'bg-emerald-50 border-emerald-200/80 text-emerald-600',
        'amber' => 'bg-amber-50 border-amber-200/80 text-amber-600',
        'rose' => 'bg-rose-50 border-rose-200/80 text-rose-600',
        'sky' => 'bg-sky-50 border-sky-200/80 text-sky-600',
    ][$color] ?? 'bg-indigo-50 border-indigo-200/80 text-indigo-600';

    // Map named icon or legacy emoji to modern Heroicons outline SVGs
    $iconName = (string) $icon;
@endphp

<div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-between transition hover:shadow-sm">
    <div class="min-w-0 pr-2">
        <span class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1 truncate">{{ $title }}</span>
        <div class="text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $value }}</div>
        @if($subtitle)
            <span class="block mt-1 text-xs text-slate-500 font-medium truncate">{{ $subtitle }}</span>
        @endif
    </div>

    @if($icon)
        <div class="w-12 h-12 rounded-2xl border {{ $colorClasses }} flex items-center justify-center flex-shrink-0 shadow-xs">
            @if(in_array($iconName, ['users', '👥']))
                <svg class="w-6 h-6 stroke-[1.75]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            @elseif(in_array($iconName, ['clock', '⏱️', '⏱']))
                <svg class="w-6 h-6 stroke-[1.75]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @elseif(in_array($iconName, ['document-text', 'document', '📑', '📄']))
                <svg class="w-6 h-6 stroke-[1.75]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            @elseif(in_array($iconName, ['banknotes', 'money', 'payroll', '🏦', '💰']))
                <svg class="w-6 h-6 stroke-[1.75]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            @elseif(in_array($iconName, ['check-circle', 'check', '✅']))
                <svg class="w-6 h-6 stroke-[1.75]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @elseif(in_array($iconName, ['exclamation-triangle', 'alert', 'warning', '⚠️']))
                <svg class="w-6 h-6 stroke-[1.75]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            @elseif(in_array($iconName, ['x-circle', 'cross', 'absent', '❌']))
                <svg class="w-6 h-6 stroke-[1.75]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @elseif(in_array($iconName, ['calendar', 'date', '🗓️', '📅']))
                <svg class="w-6 h-6 stroke-[1.75]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            @elseif(in_array($iconName, ['sun', 'vacation', 'leave', '🏖️']))
                <svg class="w-6 h-6 stroke-[1.75]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            @elseif(in_array($iconName, ['trending-down', '📉']))
                <svg class="w-6 h-6 stroke-[1.75]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                </svg>
            @elseif(in_array($iconName, ['briefcase', 'department', '🏢']))
                <svg class="w-6 h-6 stroke-[1.75]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            @elseif(in_array($iconName, ['shield', 'admin', 'hr', '👑', '🛡️']))
                <svg class="w-6 h-6 stroke-[1.75]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            @else
                {!! $icon !!}
            @endif
        </div>
    @endif
</div>
