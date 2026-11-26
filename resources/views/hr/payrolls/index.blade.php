<x-app-layout title="Manajemen Penggajian (Payroll Engine)">
    <x-slot name="breadcrumb">
        <a href="{{ route('hr.dashboard') }}" class="hover:text-slate-600 transition">HR Portal</a>
        <span>&rsaquo;</span>
        <span class="text-slate-700">Penggajian (Payroll)</span>
    </x-slot>

    <!-- Top Action & Summary Bar -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 rounded-2xl bg-gradient-to-r from-indigo-900 via-indigo-800 to-slate-900 text-white shadow-md relative overflow-hidden"
         x-data="{ generateModalOpen: false }">
        <div class="relative z-10">
            <span class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-white/10 text-indigo-200 border border-white/20 mb-2">
                Kalkulator Penggajian Otomatis
            </span>
            <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">Daftar Batch Payroll Karyawan</h2>
            <p class="text-xs text-indigo-200 mt-1">Kalkulasi gaji pokok, tunjangan, potongan absensi alpa, dan denda keterlambatan secara atomik.</p>
        </div>

        <div class="relative z-10">
            <button type="button" @click="generateModalOpen = true"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-500 hover:bg-indigo-600 text-white font-bold text-xs shadow-md transition active:scale-95">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span>Generate Payroll Bulanan</span>
            </button>
        </div>

        <!-- Generate Payroll Modal (Alpine.js) -->
        <div x-show="generateModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-sm text-slate-800">
            <div @click.away="generateModalOpen = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h3 class="text-base font-bold text-slate-900">Generate Batch Payroll Bulanan</h3>
                    <button type="button" @click="generateModalOpen = false" aria-label="Tutup" class="text-slate-400 hover:text-slate-600 p-2 min-h-[44px] min-w-[44px] flex items-center justify-center rounded-lg">&times;</button>
                </div>

                <form action="{{ route('hr.payrolls.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="month" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Bulan Penggajian <span class="text-rose-500">*</span></label>
                        @php
                            $monthOptions = [];
                            for ($m = 1; $m <= 12; $m++) {
                                $monthOptions[] = [
                                    'value' => (string) $m,
                                    'label' => \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F')
                                ];
                            }
                        @endphp
                        <x-custom-select id="month" name="month" :options="$monthOptions" :value="date('n')" required />
                    </div>

                    <div>
                        <label for="year" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Tahun Penggajian <span class="text-rose-500">*</span></label>
                        <input type="number" id="year" name="year" value="{{ date('Y') }}" min="2020" max="2035" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-800">
                        <span class="font-bold">Info:</span> Sistem akan menghitung akumulasi keterlambatan menit dan absensi alpa dari data presensi bulan terpilih.
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="generateModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs">
                            Kalkulasi & Buat Draft
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Payroll Runs Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/75 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-semibold whitespace-nowrap">
                    <tr>
                        <th class="py-3 px-6">Periode Penggajian</th>
                        <th class="py-3 px-4">Tanggal Kalkulasi</th>
                        <th class="py-3 px-4">Diproses Oleh</th>
                        <th class="py-3 px-4">Total Karyawan</th>
                        <th class="py-3 px-4">Total Pengeluaran Bersih</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($payrolls as $p)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-6 font-medium text-slate-900">
                                <div class="font-bold text-sm text-indigo-700">
                                    {{ \Carbon\Carbon::create($p->year, $p->month, 1)->translatedFormat('F Y') }}
                                </div>
                                <div class="text-[11px] text-slate-400">Batch #{{ $p->id }}</div>
                            </td>
                            <td class="py-4 px-4">
                                {{ $p->processed_at ? $p->processed_at->translatedFormat('d M Y H:i') : '-' }}
                            </td>
                            <td class="py-4 px-4 font-semibold text-slate-800">
                                {{ $p->processor->name ?? 'System' }}
                            </td>
                            <td class="py-4 px-4 font-bold text-slate-900">
                                {{ $p->items_count }} Karyawan
                            </td>
                            <td class="py-4 px-4 font-mono font-bold text-slate-900">
                                Rp {{ number_format($p->total_net_salary ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4">
                                <x-status-badge :status="$p->status" />
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('hr.payrolls.show', $p) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs border border-indigo-200 transition shadow-2xs">
                                    <span>Lihat Rincian</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8">
                                <x-empty-state title="Belum ada batch payroll" description="Gunakan tombol Generate Payroll Bulanan di atas untuk membuat kalkulasi pertama." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payrolls->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $payrolls->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
