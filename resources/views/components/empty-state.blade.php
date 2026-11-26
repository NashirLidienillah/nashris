@props([
    'title' => 'Belum ada data',
    'description' => 'Data untuk kategori ini belum tersedia pada sistem.',
    'icon' => null
])

<div class="p-8 sm:p-10 text-center rounded-2xl bg-white border border-dashed border-slate-300 my-4">
    <div class="w-12 h-12 rounded-2xl bg-slate-100 border border-slate-200/80 text-slate-400 flex items-center justify-center mx-auto mb-3 shadow-xs">
        @if($icon && !str_contains((string)$icon, '📭'))
            {!! $icon !!}
        @else
            <svg class="w-6 h-6 stroke-[1.75]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
        @endif
    </div>
    <h3 class="text-sm font-bold text-slate-800">{{ $title }}</h3>
    <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">{{ $description }}</p>
    @if(isset($action))
        <div class="mt-4">
            {{ $action }}
        </div>
    @endif
</div>
