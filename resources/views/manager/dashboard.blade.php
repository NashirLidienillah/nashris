<x-app-layout title="Dashboard Supervisi Tim">
    <x-slot name="breadcrumb">
        <span>NasHRIS</span>
        <span>&rsaquo;</span>
        <span class="text-slate-700">Manager Supervisi</span>
    </x-slot>

    <!-- Welcome Card -->
    <div class="mb-8 p-6 rounded-2xl bg-gradient-to-r from-amber-600 to-amber-800 text-white shadow-md relative overflow-hidden">
        <div class="relative z-10">
            <span class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-white/20 text-white border border-white/20 mb-2">
                Manager Portal &bull; {{ $managerEmp?->department->name ?? 'Departemen' }}
            </span>
            <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">Selamat Datang, {{ auth()->user()->name }}!</h2>
            <p class="text-xs text-amber-100 mt-1">Pantau kehadiran anggota divisi Anda dan tinjau permohonan cuti tahap pertama sebelum diajukan ke HR.</p>
        </div>
    </div>

    <!-- Summary KPI -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
        <x-stat-card title="Anggota Tim" :value="$stats['team_members']" icon="👥" color="amber" subtitle="Karyawan bawahan langsung" />
        <x-stat-card title="Hadir Hari Ini" :value="$stats['team_present_today']" icon="⏱️" color="emerald" subtitle="Presensi tercatat" />
        <x-stat-card title="Menunggu Review Cuti" :value="$stats['pending_approval']" icon="📑" color="rose" subtitle="Persetujuan Tahap 1" />
    </div>

    <!-- Pending Leave Approvals for Team -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Pengajuan Cuti Menunggu Review Tahap 1</h3>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-semibold text-slate-500">Departemen: {{ $managerEmp?->department->name ?? '-' }}</span>
                <a href="{{ route('manager.leaves.index') }}" class="text-xs font-bold text-amber-700 hover:text-amber-800 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200 transition">
                    Buka Approval Tim &rsaquo;
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="py-3 px-6">Nama Karyawan</th>
                        <th class="py-3 px-4">Tipe Cuti</th>
                        <th class="py-3 px-4">Rentang Tanggal</th>
                        <th class="py-3 px-4">Durasi</th>
                        <th class="py-3 px-4">Alasan</th>
                        <th class="py-3 px-6 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($pendingLeaves as $leave)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-6 font-medium text-slate-900">
                                <div class="font-bold">{{ $leave->employee->user->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $leave->employee->employee_code }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-800">{{ $leave->leave_type }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                {{ \Carbon\Carbon::parse($leave->start_date)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($leave->end_date)->translatedFormat('d M Y') }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $leave->total_days }} Hari
                            </td>
                            <td class="py-3.5 px-4 max-w-xs truncate text-slate-600">
                                {{ $leave->reason }}
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <x-status-badge :status="$leave->status" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                Tidak ada pengajuan cuti yang menunggu persetujuan Manager saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
