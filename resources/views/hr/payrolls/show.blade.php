<x-app-layout title="Rincian Payroll & Slip Gaji">
    <x-slot name="breadcrumb">
        <a href="{{ route('hr.dashboard') }}" class="hover:text-slate-600 transition">HR Portal</a>
        <span>&rsaquo;</span>
        <a href="{{ route('hr.payrolls.index') }}" class="hover:text-slate-600 transition">Penggajian</a>
        <span>&rsaquo;</span>
        <span class="text-slate-700">{{ \Carbon\Carbon::create($payroll->year, $payroll->month, 1)->translatedFormat('F Y') }}</span>
    </x-slot>

    <!-- Header Card & Finalization Action -->
    <div class="mb-6 p-6 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Payroll Batch #{{ $payroll->id }}</span>
                <x-status-badge :status="$payroll->status" />
            </div>
            <h2 class="text-xl font-extrabold text-slate-900">
                Periode {{ \Carbon\Carbon::create($payroll->year, $payroll->month, 1)->translatedFormat('F Y') }}
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Diproses oleh <strong>{{ $payroll->processor->name ?? 'System' }}</strong> pada {{ $payroll->processed_at ? $payroll->processed_at->translatedFormat('d M Y H:i') : '-' }} WIB
            </p>
        </div>

        <div class="flex items-center gap-3">
            @if($payroll->status === 'Draft')
                <form action="{{ route('hr.payrolls.finalize', $payroll) }}" method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi payroll ini menjadi Paid? Setelah Paid, karyawan dapat melihat dan mengunduh slip gaji mereka.')">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>Finalisasi Pembayaran (Mark as Paid)</span>
                    </button>
                </form>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>Status Final (Telah Dibayarkan)</span>
                </span>
            @endif
        </div>
    </div>

    <!-- Summary KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <x-stat-card title="Total Penerimaan Kotor" :value="'Rp ' . number_format($totals['gross'], 0, ',', '.')" icon="💰" color="indigo" subtitle="Gaji pokok + tunjangan" />
        <x-stat-card title="Potongan Terlambat" :value="'Rp ' . number_format($totals['late'], 0, ',', '.')" icon="⏱️" color="amber" subtitle="Total denda menit telat" />
        <x-stat-card title="Potongan Alpa" :value="'Rp ' . number_format($totals['absence'], 0, ',', '.')" icon="❌" color="rose" subtitle="Total denda ketidakhadiran" />
        <x-stat-card title="Total Pengeluaran Bersih" :value="'Rp ' . number_format($totals['net'], 0, ',', '.')" icon="🏦" color="emerald" subtitle="Take home pay seluruh staf" />
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs mb-6">
        <form method="GET" action="{{ route('hr.payrolls.show', $payroll) }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-3 flex-1">
                <select name="department_id" class="px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Departemen</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>

                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / kode karyawan..."
                       class="px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 flex-1 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('hr.payrolls.show', $payroll) }}" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-600 font-semibold hover:bg-slate-50">Reset</a>
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold">Filter</button>
            </div>
        </form>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/75 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="py-3 px-6">Karyawan</th>
                        <th class="py-3 px-4">Departemen</th>
                        <th class="py-3 px-4">Gaji Pokok</th>
                        <th class="py-3 px-4">Tunjangan</th>
                        <th class="py-3 px-4">Pot. Telat</th>
                        <th class="py-3 px-4">Pot. Alpa</th>
                        <th class="py-3 px-4">Gaji Bersih</th>
                        <th class="py-3 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($items as $item)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-6 font-medium text-slate-900">
                                <div class="font-bold">{{ $item->employee->user->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $item->employee->employee_code }} &bull; {{ $item->employee->position->title ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-800">
                                {{ $item->employee->department->name ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-medium text-slate-800">
                                Rp {{ number_format($item->base_salary, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-medium text-slate-800">
                                Rp {{ number_format($item->allowances, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-semibold {{ $item->late_deduction > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                                {{ $item->late_deduction > 0 ? '-Rp ' . number_format($item->late_deduction, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-semibold {{ $item->absence_deduction > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                                {{ $item->absence_deduction > 0 ? '-Rp ' . number_format($item->absence_deduction, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-extrabold text-indigo-700">
                                Rp {{ number_format($item->net_salary, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <a href="{{ route('hr.payrolls.pdf', $item) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-300 hover:border-indigo-400 hover:text-indigo-600 text-xs font-semibold text-slate-700 bg-white transition shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    <span>PDF</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8">
                                <x-empty-state title="Tidak ada data gaji" description="Data karyawan pada batch ini tidak ditemukan sesuai filter." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
