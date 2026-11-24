<x-app-layout title="Rekapitulasi & Koreksi Presensi Karyawan">
    <x-slot name="breadcrumb">
        <a href="{{ route('hr.dashboard') }}" class="hover:text-slate-600 transition">HR Portal</a>
        <span>&rsaquo;</span>
        <span class="text-slate-700">Presensi & Shift</span>
    </x-slot>

    <!-- Filter Bar Form -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs mb-6">
        <form method="GET" action="{{ route('hr.attendance.index') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-4 items-end text-xs">
            <div>
                <label for="department_id" class="block font-bold text-slate-700 mb-1">Departemen</label>
                <select id="department_id" name="department_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Departemen</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>
                            {{ $d->name }} ({{ $d->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="start_date" class="block font-bold text-slate-700 mb-1">Dari Tanggal</label>
                <input type="date" id="start_date" name="start_date" value="{{ $startDate }}"
                       class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="end_date" class="block font-bold text-slate-700 mb-1">Sampai Tanggal</label>
                <input type="date" id="end_date" name="end_date" value="{{ $endDate }}"
                       class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="status" class="block font-bold text-slate-700 mb-1">Status Kehadiran</label>
                <select id="status" name="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="Present" {{ request('status') === 'Present' ? 'selected' : '' }}>Present (Tepat Waktu)</option>
                    <option value="Late" {{ request('status') === 'Late' ? 'selected' : '' }}>Late (Terlambat)</option>
                    <option value="On Leave" {{ request('status') === 'On Leave' ? 'selected' : '' }}>On Leave (Cuti / Izin)</option>
                    <option value="Absent" {{ request('status') === 'Absent' ? 'selected' : '' }}>Absent (Alpa)</option>
                </select>
            </div>

            <div>
                <label for="search" class="block font-bold text-slate-700 mb-1">Cari Karyawan</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Nama / NIK..."
                       class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="sm:col-span-5 flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <a href="{{ route('hr.attendance.index') }}" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 font-semibold hover:bg-slate-50 transition">
                    Reset Filter
                </a>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition shadow-xs">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Attendance Table & Manual Correction Modal -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden"
         x-data="{ editModalOpen: false, editActionUrl: '', employeeName: '', attendanceDate: '', clockIn: '', clockOut: '', lateMinutes: 0, status: 'Present', notes: '' }">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/75 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="py-3 px-6">Tanggal</th>
                        <th class="py-3 px-4">Karyawan</th>
                        <th class="py-3 px-4">Departemen</th>
                        <th class="py-3 px-4">Shift</th>
                        <th class="py-3 px-4">Masuk</th>
                        <th class="py-3 px-4">Keluar</th>
                        <th class="py-3 px-4">Terlambat</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-6 text-right">Aksi</th>
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
                                <div class="text-[11px] text-slate-400 font-mono">{{ $item->employee->employee_code }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-800">
                                {{ $item->employee->department->name ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                {{ $item->shift->name ?? '-' }}
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
                            <td class="py-3.5 px-6 text-right">
                                <button type="button"
                                        @click="editModalOpen = true;
                                                editActionUrl = '{{ route('hr.attendance.update', $item) }}';
                                                employeeName = @js($item->employee->user->name ?? '');
                                                attendanceDate = '{{ $item->date->format('d/m/Y') }}';
                                                clockIn = @js($item->clock_in ?? '');
                                                clockOut = @js($item->clock_out ?? '');
                                                lateMinutes = {{ $item->late_minutes ?? 0 }};
                                                status = @js($item->status);
                                                notes = @js($item->notes ?? '');"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-slate-300 hover:border-indigo-400 hover:text-indigo-600 text-xs font-semibold text-slate-700 bg-white transition shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    <span>Koreksi</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8">
                                <x-empty-state title="Data presensi tidak ditemukan" description="Tidak ada rekaman absensi yang cocok dengan filter yang ditentukan." />
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

        <!-- Manual Attendance Correction Modal -->
        <div x-show="editModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div @click.away="editModalOpen = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Koreksi Manual Data Presensi</h3>
                        <p class="text-xs text-slate-500 mt-0.5"><span x-text="employeeName"></span> &bull; Tanggal <span x-text="attendanceDate"></span></p>
                    </div>
                    <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">&times;</button>
                </div>

                <form :action="editActionUrl" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="modal_clock_in" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Jam Masuk</label>
                            <input type="text" id="modal_clock_in" name="clock_in" x-model="clockIn" placeholder="08:00:00"
                                   class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="modal_clock_out" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Jam Keluar</label>
                            <input type="text" id="modal_clock_out" name="clock_out" x-model="clockOut" placeholder="17:00:00"
                                   class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="modal_late_minutes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Keterlambatan (Menit)</label>
                            <input type="number" id="modal_late_minutes" name="late_minutes" x-model="lateMinutes" min="0" required
                                   class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="modal_status" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Status Kehadiran</label>
                            <select id="modal_status" name="status" x-model="status" required
                                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="Present">Present</option>
                                <option value="Late">Late</option>
                                <option value="On Leave">On Leave</option>
                                <option value="Absent">Absent</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="modal_notes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Catatan Koreksi</label>
                        <textarea id="modal_notes" name="notes" x-model="notes" rows="2" placeholder="Alasan koreksi absensi..."
                                  class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs">
                            Simpan Koreksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
