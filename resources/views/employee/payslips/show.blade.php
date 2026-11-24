<x-app-layout title="Pratinjau Rincian Slip Gaji">
    <x-slot name="breadcrumb">
        <a href="{{ route('employee.dashboard') }}" class="hover:text-slate-600 transition">Portal ESS</a>
        <span>&rsaquo;</span>
        <a href="{{ route('employee.payslips.index') }}" class="hover:text-slate-600 transition">Slip Gaji</a>
        <span>&rsaquo;</span>
        <span class="text-slate-700">Rincian</span>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <!-- Top Action Bar -->
        <div class="mb-4 flex items-center justify-between">
            <a href="{{ route('employee.payslips.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                <span>Kembali ke Daftar</span>
            </a>

            <a href="{{ route('employee.payslips.download', $item) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition active:scale-95">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                <span>Unduh Berkas PDF Resmi</span>
            </a>
        </div>

        <!-- Payslip Card Display -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <!-- Header Brand -->
            <div class="flex items-center justify-between pb-6 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md shadow-indigo-600/30">
                        N
                    </div>
                    <div>
                        <span class="text-xl font-extrabold tracking-tight text-slate-900">Nas<span class="text-indigo-600">HRIS</span></span>
                        <span class="block text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Slip Gaji Elektronik Resmi</span>
                    </div>
                </div>

                <div class="text-right">
                    <x-status-badge :status="$item->payroll->status" />
                    <div class="text-xs font-bold text-slate-700 mt-1">
                        {{ \Carbon\Carbon::create($item->payroll->year, $item->payroll->month, 1)->translatedFormat('F Y') }}
                    </div>
                </div>
            </div>

            <!-- Employee Info -->
            <div class="py-5 border-b border-slate-100 grid grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-slate-400 block">Nama Karyawan:</span>
                    <span class="font-bold text-slate-900 text-sm">{{ $item->employee->user->name }}</span>
                    <span class="text-[11px] text-slate-500 font-mono block">{{ $item->employee->employee_code }}</span>
                </div>
                <div class="text-right">
                    <span class="text-slate-400 block">Departemen & Jabatan:</span>
                    <span class="font-bold text-slate-900">{{ $item->employee->department->name }}</span>
                    <span class="text-[11px] text-slate-500 block">{{ $item->employee->position->title }}</span>
                </div>
            </div>

            <!-- Components Breakdown -->
            <div class="py-6 space-y-6">
                <!-- Penerimaan -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Penerimaan (Earnings)</h4>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-600">Gaji Pokok (Base Salary)</span>
                            <span class="font-mono font-semibold text-slate-900">Rp {{ number_format($item->base_salary, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-600">Tunjangan Tetap (Fixed Allowance)</span>
                            <span class="font-mono font-semibold text-slate-900">Rp {{ number_format($item->allowances, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between pt-1 font-bold text-slate-900">
                            <span>Subtotal Penerimaan:</span>
                            <span class="font-mono text-indigo-700">Rp {{ number_format($item->base_salary + $item->allowances, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Potongan -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Potongan (Deductions)</h4>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-600">Potongan Keterlambatan (Late)</span>
                            <span class="font-mono font-semibold text-rose-600">Rp {{ number_format($item->late_deduction, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span class="text-slate-600">Potongan Ketidakhadiran (Absence)</span>
                            <span class="font-mono font-semibold text-rose-600">Rp {{ number_format($item->absence_deduction, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between pt-1 font-bold text-slate-900">
                            <span>Subtotal Potongan:</span>
                            <span class="font-mono text-rose-600">Rp {{ number_format($item->late_deduction + $item->absence_deduction, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Net Salary Take Home Pay Banner -->
            <div class="p-5 rounded-xl bg-gradient-to-r from-indigo-50 to-emerald-50 border border-indigo-200/80 flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-800 block">Total Gaji Bersih Diterima</span>
                    <span class="text-xs text-slate-500">Take Home Pay</span>
                </div>
                <div class="text-xl sm:text-2xl font-extrabold text-indigo-700 font-mono">
                    Rp {{ number_format($item->net_salary, 0, ',', '.') }}
                </div>
            </div>

            <!-- Disclaimer Footer -->
            <div class="mt-6 pt-4 border-t border-slate-100 text-[10px] text-slate-400 text-center">
                Dokumen ini diterbitkan secara otomatis oleh sistem NasHRIS dan sah tanpa memerlukan tanda tangan basah.
            </div>
        </div>
    </div>
</x-app-layout>
