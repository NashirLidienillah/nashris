<x-app-layout title="Audit Log & Riwayat Aktivitas Sistem">
    <x-slot name="breadcrumb">
        <a href="{{ route('hr.dashboard') }}" class="hover:text-slate-600 transition">HR Portal</a>
        <span>&rsaquo;</span>
        <span class="text-slate-700">Audit Log</span>
    </x-slot>

    <!-- Header Card -->
    <div class="mb-6 p-6 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Sistem Keamanan & Kepatuhan</span>
            </div>
            <h2 class="text-lg font-bold text-slate-900">Audit Trail Riwayat Aktivitas Pengguna</h2>
            <p class="text-xs text-slate-500 mt-0.5">Mencatat mutasi data, tindakan persetujuan, dan transaksi penting beserta aktor pengubahnya secara transparan.</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs mb-6">
        <form method="GET" action="{{ route('hr.audit-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end text-xs">
            <div>
                <label for="log_name" class="block font-bold text-slate-700 mb-1">Kategori Modul</label>
                <select id="log_name" name="log_name" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Modul</option>
                    @foreach($logNames as $name)
                        <option value="{{ $name }}" {{ request('log_name') === $name ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $name)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="event" class="block font-bold text-slate-700 mb-1">Jenis Aksi</label>
                <select id="event" name="event" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Aksi</option>
                    <option value="created" {{ request('event') === 'created' ? 'selected' : '' }}>Created (Penambahan)</option>
                    <option value="updated" {{ request('event') === 'updated' ? 'selected' : '' }}>Updated (Perubahan)</option>
                    <option value="deleted" {{ request('event') === 'deleted' ? 'selected' : '' }}>Deleted (Penghapusan)</option>
                </select>
            </div>

            <div>
                <label for="search" class="block font-bold text-slate-700 mb-1">Cari Keterangan / Aktor</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..."
                       class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('hr.audit-logs.index') }}" class="w-1/2 py-2 text-center rounded-xl border border-slate-300 text-slate-600 font-semibold hover:bg-slate-50">Reset</a>
                <button type="submit" class="w-1/2 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold">Filter</button>
            </div>
        </form>
    </div>

    <!-- Audit Log Table & Details Modal (Alpine.js) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden"
         x-data="{ detailModalOpen: false, modalTitle: '', modalProps: '' }">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/75 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="py-3 px-6">Waktu Kejadian</th>
                        <th class="py-3 px-4">Pengguna / Aktor</th>
                        <th class="py-3 px-4">Modul</th>
                        <th class="py-3 px-4">Aksi Event</th>
                        <th class="py-3 px-4">Deskripsi Perubahan</th>
                        <th class="py-3 px-6 text-right">Data Mutasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($activities as $act)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-6 font-medium text-slate-900">
                                <div class="font-bold">{{ $act->created_at->translatedFormat('d M Y H:i:s') }}</div>
                                <div class="text-[11px] text-slate-400">{{ $act->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-900">
                                @if($act->causer)
                                    <div class="font-bold">{{ $act->causer->name }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $act->causer->email }}</div>
                                @else
                                    <span class="text-slate-400 font-italic">System / Anonymous</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700 uppercase">
                                    {{ $act->log_name ?? 'general' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                    $ev = $act->event ?? 'event';
                                    $evColor = match($ev) {
                                        'created' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'updated' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'deleted' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $evColor }} uppercase tracking-wider">
                                    {{ $ev }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-800 max-w-sm">
                                {{ $act->description }}
                                @if($act->subject_type)
                                    <span class="block text-[10px] text-slate-400 font-mono truncate">
                                        {{ class_basename($act->subject_type) }} #{{ $act->subject_id }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                @if($act->properties && $act->properties->count() > 0)
                                    <button type="button"
                                            @click="detailModalOpen = true;
                                                    modalTitle = 'Rincian Perubahan: ' + @js($act->description);
                                                    modalProps = JSON.stringify(@js($act->properties), null, 2);"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-slate-300 hover:border-indigo-400 hover:text-indigo-600 text-xs font-semibold text-slate-700 bg-white transition shadow-2xs">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        <span>Diff JSON</span>
                                    </button>
                                @else
                                    <span class="text-slate-400 text-[11px]">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8">
                                <x-empty-state title="Belum ada riwayat aktivitas" description="Seluruh aktivitas pengguna akan tercatat secara otomatis di sini." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $activities->links() }}
            </div>
        @endif

        <!-- JSON Diff Modal -->
        <div x-show="detailModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div @click.away="detailModalOpen = false" class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-xl border border-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h3 class="text-sm font-bold text-slate-900" x-text="modalTitle">Rincian Perubahan Data</h3>
                    <button type="button" @click="detailModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1">&times;</button>
                </div>

                <div class="bg-slate-900 text-slate-100 p-4 rounded-xl font-mono text-[11px] overflow-x-auto max-h-96">
                    <pre x-text="modalProps"></pre>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex justify-end">
                    <button type="button" @click="detailModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
