<x-app-layout title="Presensi Harian & Riwayat Kehadiran">
    <x-slot name="breadcrumb">
        <a href="{{ route('employee.dashboard') }}" class="hover:text-slate-600 transition">Portal ESS</a>
        <span>&rsaquo;</span>
        <span class="text-slate-700">Presensi Harian</span>
    </x-slot>

    <!-- Top Grid: Interactive Clock Widget & Month KPI -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Interactive Clock-In / Clock-Out Widget -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Presensi Hari Ini</span>
                    <span class="text-xs font-mono font-bold text-indigo-700 bg-indigo-50 border border-indigo-100 px-2.5 py-1 rounded-lg">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d M Y') }}
                    </span>
                </div>

                <!-- Clock Display -->
                <div class="text-center py-4 bg-slate-50/80 rounded-xl border border-slate-200/80 mb-5"
                     x-data="{ currentTime: '{{ \Carbon\Carbon::now()->format('H:i:s') }}' }"
                     x-init="setInterval(() => {
                         const d = new Date();
                         currentTime = String(d.getHours()).padStart(2, '0') + ':' +
                                       String(d.getMinutes()).padStart(2, '0') + ':' +
                                       String(d.getSeconds()).padStart(2, '0');
                     }, 1000)">
                    <div class="text-3xl font-extrabold text-slate-900 tracking-tight font-mono" x-text="currentTime">
                        {{ \Carbon\Carbon::now()->format('H:i:s') }}
                    </div>
                    <div class="text-xs text-slate-500 mt-1">
                        Shift: <span class="font-semibold text-slate-700">{{ $employee->shift->name }}</span> ({{ $employee->shift->start_time }} - {{ $employee->shift->end_time }})
                    </div>

                    <div class="mt-3">
                        @if($todayAttendance)
                            <x-status-badge :status="$todayAttendance->status" />
                            @if($todayAttendance->late_minutes > 0)
                                <span class="block text-[11px] text-amber-600 font-semibold mt-1">
                                    Terlambat {{ $todayAttendance->late_minutes }} menit
                                </span>
                            @endif
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                Belum Presensi Masuk
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-2 gap-3">
                <form action="{{ route('employee.attendance.clock-in') }}" method="POST">
                    @csrf
                    <button type="submit"
                        @if($todayAttendance && $todayAttendance->clock_in) disabled @endif
                        class="w-full py-2.5 px-4 rounded-xl text-xs font-bold text-white transition flex items-center justify-center gap-2
                            {{ ($todayAttendance && $todayAttendance->clock_in)
                                ? 'bg-slate-200 text-slate-400 cursor-not-allowed shadow-none'
                                : 'bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 active:scale-95' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                        <span>{{ ($todayAttendance && $todayAttendance->clock_in) ? 'Clocked In' : 'Clock In' }}</span>
                    </button>
                </form>

                <form action="{{ route('employee.attendance.clock-out') }}" method="POST">
                    @csrf
                    <button type="submit"
                        @if(! $todayAttendance || ! $todayAttendance->clock_in || $todayAttendance->clock_out) disabled @endif
                        class="w-full py-2.5 px-4 rounded-xl text-xs font-bold text-white transition flex items-center justify-center gap-2
                            {{ (! $todayAttendance || ! $todayAttendance->clock_in || $todayAttendance->clock_out)
                                ? 'bg-slate-200 text-slate-400 cursor-not-allowed shadow-none'
                                : 'bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-600/20 active:scale-95' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        <span>{{ ($todayAttendance && $todayAttendance->clock_out) ? 'Clocked Out' : 'Clock Out' }}</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Month Summary Cards (2 cols) -->
        <div class="lg:col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-4">
            <x-stat-card title="Hadir Tepat Waktu" :value="$summary['present']" icon="check-circle" color="emerald" subtitle="Hari bulan ini" />
            <x-stat-card title="Keterlambatan" :value="$summary['late']" icon="exclamation-triangle" color="amber" subtitle="{{ $summary['late_minutes'] }} total menit" />
            <x-stat-card title="Cuti / Izin" :value="$summary['on_leave']" icon="sun" color="sky" subtitle="Hari disetujui" />
            <x-stat-card title="Tanpa Keterangan" :value="$summary['absent']" icon="x-circle" color="rose" subtitle="Hari alpa" />
        </div>
    </div>

    <!-- Attendance History Table Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <!-- Table Header & Month Filter -->
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Riwayat Kehadiran Bulanan</h3>
                <p class="text-xs text-slate-500">Menampilkan seluruh catatan absensi pada periode yang dipilih.</p>
            </div>

            <form method="GET" action="{{ route('employee.attendance.index') }}" class="flex items-center gap-2">
                <label for="month" class="text-xs font-semibold text-slate-600">Periode:</label>
                <input type="month" id="month" name="month" value="{{ $selectedMonth }}"
                       onchange="this.form.submit()"
                       class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </form>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/75 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-semibold whitespace-nowrap">
                    <tr>
                        <th class="py-3 px-6">Tanggal</th>
                        <th class="py-3 px-4">Shift Kerja</th>
                        <th class="py-3 px-4">Jam Masuk</th>
                        <th class="py-3 px-4">Jam Keluar</th>
                        <th class="py-3 px-4">Keterlambatan</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-6 text-right">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($attendances as $item)
                        <tr class="hover:bg-slate-50/60 transition {{ $item->date->isToday() ? 'bg-indigo-50/30' : '' }}">
                            <td class="py-3.5 px-6 font-medium text-slate-900">
                                <div class="font-bold">{{ $item->date->translatedFormat('d F Y') }}</div>
                                <div class="text-[11px] text-slate-400 font-normal">{{ $item->date->translatedFormat('l') }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-medium text-slate-700">{{ $item->shift->name ?? '-' }}</span>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $item->shift->start_time ?? '-' }} - {{ $item->shift->end_time ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-semibold {{ $item->clock_in ? 'text-slate-900' : 'text-slate-300' }}">
                                {{ $item->clock_in ?? '--:--' }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-semibold {{ $item->clock_out ? 'text-slate-900' : 'text-slate-300' }}">
                                {{ $item->clock_out ?? '--:--' }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($item->late_minutes > 0)
                                    <span class="inline-flex items-center gap-1 font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                                        +{{ $item->late_minutes }} mnt
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <x-status-badge :status="$item->status" />
                            </td>
                            <td class="py-3.5 px-6 text-right text-slate-500 max-w-xs truncate">
                                {{ $item->notes ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8">
                                <x-empty-state title="Belum ada data presensi" description="Tidak ada rekaman kehadiran untuk periode bulan ini." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
