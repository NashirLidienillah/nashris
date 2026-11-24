<x-app-layout title="Riwayat Slip Gaji Saya">
    <x-slot name="breadcrumb">
        <a href="{{ route('employee.dashboard') }}" class="hover:text-slate-600 transition">Portal ESS</a>
        <span>&rsaquo;</span>
        <span class="text-slate-700">Slip Gaji</span>
    </x-slot>

    <!-- Header Card -->
    <div class="mb-6 p-6 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-sky-700 bg-sky-50 px-2.5 py-0.5 rounded-full border border-sky-200">
                Employee Self-Service (ESS)
            </span>
            <h2 class="text-lg font-bold text-slate-900 mt-1">Daftar Slip Gaji Resmi</h2>
            <p class="text-xs text-slate-500">Hanya slip gaji yang telah berstatus <strong>Paid</strong> yang dapat dilihat dan diunduh secara resmi.</p>
        </div>
    </div>

    <!-- Payslips List Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/75 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="py-3 px-6">Periode Gaji</th>
                        <th class="py-3 px-4">Gaji Pokok</th>
                        <th class="py-3 px-4">Tunjangan Tetap</th>
                        <th class="py-3 px-4">Potongan (Telat/Alpa)</th>
                        <th class="py-3 px-4">Gaji Bersih Diterima</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($payslips as $item)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-6 font-medium text-slate-900">
                                <div class="font-bold text-sm text-indigo-700">
                                    {{ \Carbon\Carbon::create($item->payroll->year, $item->payroll->month, 1)->translatedFormat('F Y') }}
                                </div>
                                <div class="text-[11px] text-slate-400">
                                    Cair pada: {{ $item->payroll->processed_at ? $item->payroll->processed_at->translatedFormat('d M Y') : '-' }}
                                </div>
                            </td>
                            <td class="py-4 px-4 font-mono font-medium text-slate-800">
                                Rp {{ number_format($item->base_salary, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4 font-mono font-medium text-slate-800">
                                Rp {{ number_format($item->allowances, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4 font-mono font-semibold {{ ($item->late_deduction + $item->absence_deduction) > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                                {{ ($item->late_deduction + $item->absence_deduction) > 0 ? '-Rp ' . number_format($item->late_deduction + $item->absence_deduction, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-4 px-4 font-mono font-extrabold text-emerald-700 text-sm">
                                Rp {{ number_format($item->net_salary, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4">
                                <x-status-badge :status="$item->payroll->status" />
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('employee.payslips.show', $item) }}"
                                       class="px-3 py-1.5 rounded-lg border border-slate-300 hover:border-slate-400 text-xs font-bold text-slate-700 bg-white transition shadow-2xs">
                                        Rincian
                                    </a>
                                    <a href="{{ route('employee.payslips.download', $item) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition active:scale-95">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        <span>Unduh PDF</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8">
                                <x-empty-state title="Belum ada slip gaji" description="Slip gaji bulanan Anda akan muncul di sini setelah diproses dan difinalisasi oleh pihak HR." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payslips->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $payslips->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
