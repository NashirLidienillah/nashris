@props([
    'title' => 'Belum ada data',
    'description' => 'Data untuk kategori ini belum tersedia pada sistem.',
    'icon' => '📭'
])

<div class="p-10 text-center rounded-2xl bg-white border border-dashed border-slate-300 my-4">
    <div class="text-4xl mb-3">{{ $icon }}</div>
    <h3 class="text-base font-bold text-slate-800">{{ $title }}</h3>
    <p class="mt-1 text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">{{ $description }}</p>
    @if(isset($action))
        <div class="mt-5">
            {{ $action }}
        </div>
    @endif
</div>
