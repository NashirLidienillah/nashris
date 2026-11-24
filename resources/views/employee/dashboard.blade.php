<x-app-layout title="Portal ESS Karyawan">
    <x-slot name="breadcrumb">
        <span>NasHRIS</span>
        <span>&rsaquo;</span>
        <span class="text-slate-700">Employee Self-Service</span>
    </x-slot>

    <!-- Welcome Hero Banner -->
    <div class="mb-8 p-6 rounded-2xl bg-gradient-to-r from-sky-700 via-sky-600 to-indigo-800 text-white shadow-md relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-white/20 text-white border border-white/20 mb-2">
                    Portal Mandiri Karyawan
                </span>
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">Halo, {{ auth()->user()->name }}!</h2>
                <p class="text-xs text-sky-100 mt-1">
                    {{ $employee?->position->title ?? 'Staff' }} &bull; Departemen {{ $employee?->department->name ?? 'N/A' }} &bull; NIK: <code class="bg-black/20 px-1.5 py-0.5 rounded font-mono">{{ $employee?->employee_code }}</code>
                </p>
            </div>

            <!-- Shift Badge -->
            <div class="bg-white/10 backdrop-blur border border-white/20 rounded-xl p-3 text-xs">
                <span class="block text-sky-200 text-[10px] uppercase font-bold tracking-wider">Jadwal Shift Anda</span>
                <div class="font-extrabold text-white mt-0.5">{{ $employee?->shift->name ?? 'Regular Office' }}</div>
                <div class="text-[11px] text-sky-100 font-mono">{{ $employee?->shift->start_time ?? '08:00' }} - {{ $employee?->shift->end_time ?? '17:00' }}</div>
            </div>
        </div>
    </div>

    <!-- Grid: ESS Status & Widgets -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Clock In/Out Widget Placeholder (Phase 4 Ready) -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Presensi Hari Ini</h3>
                <span class="text-xs font-mono font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">
                    {{ \Carbon\Carbon::now()->format('H:i') }} WIB
                </span>
            </div>

            <div class="p-5 rounded-xl bg-slate-50 border border-slate-200 text-center mb-5">
                <span class="block text-xs font-semibold text-slate-500 mb-1">Status Kehadiran</span>
                @if($todayAttendance)
                    <div class="my-2"><x-status-badge :status="$todayAttendance->status" /></div>
                    <div class="text-xs text-slate-600 mt-2">
                        Masuk: <strong class="font-mono text-slate-900">{{ $todayAttendance->clock_in ?? '-' }}</strong> &bull;
                        Keluar: <strong class="font-mono text-slate-900">{{ $todayAttendance->clock_out ?? '-' }}</strong>
                    </div>
                @else
                    <div class="text-lg font-bold text-slate-800 my-2">Belum Presensi</div>
                    <p class="text-xs text-slate-500">Gunakan tombol Clock-In di bawah sesuai jam kerja shift Anda.</p>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-3">
                <form action="{{ route('employee.attendance.clock-in') }}" method="POST">
                    @csrf
                    <button type="submit"
                        @if($todayAttendance && $todayAttendance->clock_in) disabled @endif
                        class="w-full py-2.5 min-h-[44px] rounded-xl text-xs font-bold text-white transition flex items-center justify-center gap-1.5
                            {{ ($todayAttendance && $todayAttendance->clock_in)
                                ? 'bg-slate-200 text-slate-400 cursor-not-allowed shadow-none'
                                : 'bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 active:scale-95' }}">
                        <span>{{ ($todayAttendance && $todayAttendance->clock_in) ? 'Clocked In' : 'Clock In' }}</span>
                    </button>
                </form>

                <form action="{{ route('employee.attendance.clock-out') }}" method="POST">
                    @csrf
                    <button type="submit"
                        @if(! $todayAttendance || ! $todayAttendance->clock_in || $todayAttendance->clock_out) disabled @endif
                        class="w-full py-2.5 min-h-[44px] rounded-xl text-xs font-bold text-white transition flex items-center justify-center gap-1.5
                            {{ (! $todayAttendance || ! $todayAttendance->clock_in || $todayAttendance->clock_out)
                                ? 'bg-slate-200 text-slate-400 cursor-not-allowed shadow-none'
                                : 'bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-600/20 active:scale-95' }}">
                        <span>{{ ($todayAttendance && $todayAttendance->clock_out) ? 'Clocked Out' : 'Clock Out' }}</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Leave Balance Card -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Saldo Cuti Tahun {{ date('Y') }}</h3>
                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                        {{ $leaveBalance->remaining_quota ?? 12 }} Hari Tersisa
                    </span>
                </div>

                <div class="space-y-3 mt-4">
                    <div class="flex justify-between items-center text-xs py-2 border-b border-slate-100">
                        <span class="text-slate-500">Total Kuota Tahunan:</span>
                        <span class="font-bold text-slate-900">{{ $leaveBalance->total_quota ?? 12 }} Hari</span>
                    </div>
                    <div class="flex justify-between items-center text-xs py-2 border-b border-slate-100">
                        <span class="text-slate-500">Kuota Terpakai:</span>
                        <span class="font-bold text-rose-600">{{ $leaveBalance->used_quota ?? 0 }} Hari</span>
                    </div>
                    <div class="flex justify-between items-center text-xs py-2">
                        <span class="text-slate-500">Sisa Kuota Cuti:</span>
                        <span class="font-extrabold text-emerald-700 text-sm">{{ $leaveBalance->remaining_quota ?? 12 }} Hari</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('employee.leaves.create') }}" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs text-center block shadow-md shadow-indigo-600/20 transition active:scale-95">
                    Ajukan Cuti Baru
                </a>
            </div>
        </div>

        <!-- Quick Info / Employment Profile -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Informasi Kepegawaian</h3>
            <div class="space-y-3 text-xs">
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Tipe Kontrak:</span>
                    <x-status-badge :status="$employee?->contract_type ?? 'PKWTT'" />
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Tanggal Bergabung:</span>
                    <span class="font-semibold text-slate-800">{{ $employee?->join_date ? \Carbon\Carbon::parse($employee->join_date)->translatedFormat('d M Y') : '-' }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Atasan Langsung:</span>
                    <span class="font-semibold text-slate-800">{{ $employee?->manager?->user->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1.5">
                    <span class="text-slate-500">No. Handphone:</span>
                    <span class="font-mono text-slate-800">{{ $employee?->phone ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
