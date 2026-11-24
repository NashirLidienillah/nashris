<x-app-layout title="Presensi Tim & Kedisiplinan">
    <x-slot name="breadcrumb">
        <a href="{{ route('manager.dashboard') }}" class="hover:text-slate-600 transition">Manager Portal</a>
        <span>&rsaquo;</span>
        <span class="text-slate-700">Presensi Tim</span>
    </x-slot>

    <!-- Header & Scope Badge -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-white border border-slate-200 shadow-xs">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200">
                Departemen {{ $department->name }}
            </span>
            <h2 class="text-lg font-bold text-slate-900 mt-1">Pemantauan Presensi Tim Divisi</h2>
            <p class="text-xs text-slate-500">Melihat catatan jam masuk, pulang, dan keterlambatan bawahan di divisi Anda.</p>
        </div>
    </div>

    <!-- Filter Bar Form -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs mb-6">
        <form method="GET" action="{{ route('manager.attendance.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end text-xs">
            <div>
                <label for="start_date" class="block font-bold text-slate-700 mb-1">Dari Tanggal</label>
                <input type="date" id="start_date" name="start_date" value="{{ $startDate }}"
                       class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            <div>
                <label for="end_date" class="block font-bold text-slate-700 mb-1">Sampai Tanggal</label>
                <input type="date" id="end_date" name="end_date" value="{{ $endDate }}"
                       class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            <div>
                <label for="employee_id" class="block font-bold text-slate-700 mb-1">Anggota Tim</label>
                <select id="employee_id" name="employee_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">Semua Anggota ({{ $teamMembers->count() }})</option>
                    @foreach($teamMembers as $m)
                        <option value="{{ $m->id }}" {{ request('employee_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->user->name }} ({{ $m->employee_code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block font-bold text-slate-700 mb-1">Status Kehadiran</label>
                <select id="status" name="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">Semua Status</option>
                    <option value="Present" {{ request('status') === 'Present' ? 'selected' : '' }}>Present (Tepat Waktu)</option>
                    <option value="Late" {{ request('status') === 'Late' ? 'selected' : '' }}>Late (Terlambat)</option>
                    <option value="On Leave" {{ request('status') === 'On Leave' ? 'selected' : '' }}>On Leave (Cuti / Izin)</option>
                    <option value="Absent" {{ request('status') === 'Absent' ? 'selected' : '' }}>Absent (Alpa)</option>
                </select>
            </div>

            <div class="sm:col-span-4 flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <a href="{{ route('manager.attendance.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 font-semibold hover:bg-slate-50 transition">
                    Reset Filter
                </a>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold transition shadow-xs">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/75 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="py-3 px-6">Tanggal</th>
                        <th class="py-3 px-4">Karyawan</th>
                        <th class="py-3 px-4">Shift</th>
                        <th class="py-3 px-4">Masuk</th>
                        <th class="py-3 px-4">Keluar</th>
                        <th class="py-3 px-4">Keterlambatan</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-6 text-right">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($attendances as $item)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-6 font-medium text-slate-900">
                                <div class="font-bold">{{ $item->date->translatedFormat('d M Y') }}</div>
                                <div class="text-[11px] text-slate-400 font-normal">{{ $item->date->translatedFormat('l') }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-900">
                                <div class="font-bold">{{ $item->employee->user->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $item->employee->employee_code }} &bull; {{ $item->employee->position->title ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-medium text-slate-700">{{ $item->shift->name ?? '-' }}</span>
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
                            <td colspan="8" class="py-8">
                                <x-empty-state title="Tidak ada catatan presensi" description="Tidak ditemukan riwayat kehadiran anggota tim sesuai filter yang ditentukan." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
