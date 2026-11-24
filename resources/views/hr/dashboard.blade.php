<x-app-layout title="HR Analytics Dashboard">
    <x-slot name="breadcrumb">
        <span>NasHRIS</span>
        <span>&rsaquo;</span>
        <span class="text-slate-700">HR Analytics</span>
    </x-slot>

    <!-- Welcome & Quick Action Bar -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 rounded-2xl bg-gradient-to-r from-indigo-900 via-indigo-800 to-slate-900 text-white shadow-md relative overflow-hidden">
        <div class="relative z-10">
            <span class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-white/10 text-indigo-200 border border-white/20 mb-2">
                HR Administrator Portal
            </span>
            <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">Selamat Datang, {{ auth()->user()->name }}!</h2>
            <p class="text-xs text-indigo-200 mt-1">Pantau analitik kepegawaian, tren kedisiplinan presensi 6 bulan, dan peringatan kontrak aktif secara terpadu.</p>
        </div>

        <div class="relative z-10 flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('hr.employees.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-500 hover:bg-indigo-600 text-white font-semibold text-xs shadow-md transition active:scale-95">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span>Tambah Karyawan</span>
            </a>
            <a href="{{ route('hr.payrolls.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-xs backdrop-blur transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                <span>Kelola Payroll</span>
            </a>
        </div>
    </div>

    <!-- 4 Core KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <x-stat-card title="Total Karyawan Aktif" :value="$stats['total_employees']" icon="👥" color="indigo" subtitle="Seluruh departemen" />
        <x-stat-card title="Tingkat Presensi Hari Ini" :value="$stats['attendance_rate'] . '%'" icon="⏱️" color="emerald" subtitle="{{ $stats['attendance_today'] }} dari {{ $stats['total_employees'] }} hadir" />
        <x-stat-card title="Review Cuti Menunggu HR" :value="$stats['pending_leaves']" icon="📑" color="amber" subtitle="Verifikasi tahap akhir" />
        <x-stat-card title="Estimasi Payroll Bulanan" :value="'Rp ' . number_format($stats['monthly_payroll_estimate'] / 1000000, 1, ',', '.') . ' Jt'" icon="🏦" color="sky" subtitle="Gaji pokok + tunjangan tetap" />
    </div>

    <!-- Visual Charts Grid (Chart.js) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8"
         x-data="{
             initCharts() {
                 if (typeof Chart === 'undefined') {
                     window.addEventListener('chart-loaded', () => this.initCharts(), { once: true });
                     return;
                 }

                 // 1. Department Distribution Doughnut
                 const ctxDept = document.getElementById('deptChart');
                 if (ctxDept) {
                     new Chart(ctxDept, {
                         type: 'doughnut',
                         data: {
                             labels: {{ json_encode($deptLabels) }},
                             datasets: [{
                                 data: {{ json_encode($deptCounts) }},
                                 backgroundColor: ['#4f46e5', '#06b6d4', '#10b981', '#f59e0b', '#ec4899'],
                                 borderWidth: 2,
                                 borderColor: '#ffffff',
                             }]
                         },
                         options: {
                             responsive: true,
                             maintainAspectRatio: false,
                             plugins: {
                                 legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } }
                             },
                             cutout: '65%'
                         }
                     });
                 }

                 // 2. 6-Month Attendance & Late Trend Line Chart
                 const ctxTrend = document.getElementById('trendChart');
                 if (ctxTrend) {
                     new Chart(ctxTrend, {
                         type: 'line',
                         data: {
                             labels: {{ json_encode($trendLabels) }},
                             datasets: [
                                 {
                                     label: 'Hadir (Present)',
                                     data: {{ json_encode($trendPresent) }},
                                     borderColor: '#10b981',
                                     backgroundColor: 'rgba(16, 185, 129, 0.08)',
                                     fill: true,
                                     tension: 0.35,
                                     borderWidth: 2,
                                 },
                                 {
                                     label: 'Terlambat (Late)',
                                     data: {{ json_encode($trendLate) }},
                                     borderColor: '#f59e0b',
                                     backgroundColor: 'rgba(245, 158, 11, 0.08)',
                                     fill: true,
                                     tension: 0.35,
                                     borderWidth: 2,
                                 },
                                 {
                                     label: 'Alpa (Absent)',
                                     data: {{ json_encode($trendAbsent) }},
                                     borderColor: '#ef4444',
                                     backgroundColor: 'rgba(239, 68, 68, 0.08)',
                                     fill: true,
                                     tension: 0.35,
                                     borderWidth: 2,
                                 }
                             ]
                         },
                         options: {
                             responsive: true,
                             maintainAspectRatio: false,
                             plugins: {
                                 legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } }
                             },
                             scales: {
                                 y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } },
                                 x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                             }
                         }
                     });
                 }
             }
         }"
         x-init="initCharts()">

        <!-- 6-Month Attendance & Late Trend (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-xs p-6 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Tren Kedisiplinan & Presensi (6 Bulan Terakhir)</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Komparasi tingkat kehadiran tepat waktu, keterlambatan, dan alpa.</p>
                </div>
                <span class="text-xs font-semibold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-lg">Realtime Presensi</span>
            </div>

            <div class="h-64 relative">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Department Distribution (1 col) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 flex flex-col justify-between">
            <div class="mb-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Distribusi Divisi</h3>
                <p class="text-xs text-slate-500 mt-0.5">Proporsi karyawan per departemen.</p>
            </div>

            <div class="h-64 relative">
                <canvas id="deptChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Bottom Grid: 30-Day Contract Expiry Reminder & Recent Audit Log -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- 30-Day Contract Expiry Reminder (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Peringatan Berakhir Kontrak (30 Hari ke Depan)</h3>
                </div>
                <span class="text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-full">
                    {{ $stats['expiring_contracts'] }} Karyawan
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/75 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-semibold">
                        <tr>
                            <th class="py-3 px-6">Karyawan</th>
                            <th class="py-3 px-4">Departemen / Jabatan</th>
                            <th class="py-3 px-4">Tipe Kontrak</th>
                            <th class="py-3 px-4">Tanggal Berakhir</th>
                            <th class="py-3 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($expiringEmployees as $emp)
                            @php
                                $daysLeft = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($emp->contract_end_date), false);
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-6 font-medium text-slate-900">
                                    <div class="font-bold">{{ $emp->user->name ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $emp->employee_code }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-slate-800">{{ $emp->department->name ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $emp->position->title ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <x-status-badge :status="$emp->contract_type" />
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-amber-700">
                                        {{ \Carbon\Carbon::parse($emp->contract_end_date)->translatedFormat('d M Y') }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        {{ $daysLeft >= 0 ? $daysLeft . ' hari lagi' : 'Telah jatuh tempo' }}
                                    </div>
                                </td>
                                <td class="py-3.5 px-6 text-right">
                                    <a href="{{ route('hr.employees.show', $emp) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-300 hover:border-indigo-400 hover:text-indigo-600 text-xs font-semibold text-slate-700 bg-white transition shadow-2xs">
                                        <span>Dossier</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">
                                    Tidak ada kontrak karyawan yang berakhir dalam 30 hari ke depan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Activity Audit Log Widget (1 col) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between overflow-hidden">
            <div>
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Aktivitas Sistem Terkini</h3>
                    <a href="{{ route('hr.audit-logs.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">Lihat Semua &rsaquo;</a>
                </div>

                <div class="p-6 space-y-4">
                    @forelse($recentActivities as $act)
                        <div class="flex items-start gap-3 text-xs">
                            <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-700 font-bold flex items-center justify-center flex-shrink-0 mt-0.5 border border-indigo-100">
                                {{ strtoupper(substr($act->causer->name ?? 'S', 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-slate-800 truncate">
                                    {{ $act->causer->name ?? 'System' }}
                                </div>
                                <div class="text-slate-600 truncate">{{ $act->description }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $act->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-xs text-slate-400">
                            Belum ada rekaman aktivitas audit log.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                <a href="{{ route('hr.audit-logs.index') }}" class="w-full py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs text-center block transition">
                    Buka Riwayat Audit Lengkap
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/chart.js')
    @endpush
</x-app-layout>
