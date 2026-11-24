<x-app-layout title="Pengajuan & Saldo Cuti">
    <x-slot name="breadcrumb">
        <a href="{{ route('employee.dashboard') }}" class="hover:text-slate-600 transition">Portal ESS</a>
        <span>&rsaquo;</span>
        <span class="text-slate-700">Manajemen Cuti</span>
    </x-slot>

    <!-- Top Grid: Leave Balance Cards & Quick Action -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
        <x-stat-card title="Total Kuota Cuti" :value="$leaveBalance->total_quota . ' Hari'" icon="🗓️" color="indigo" subtitle="Tahun {{ date('Y') }}" />
        <x-stat-card title="Cuti Terpakai" :value="$leaveBalance->used_quota . ' Hari'" icon="📉" color="amber" subtitle="Telah disetujui HR" />
        <x-stat-card title="Sisa Saldo Cuti" :value="$leaveBalance->remaining_quota . ' Hari'" icon="🏖️" color="emerald" subtitle="Dapat diajukan" />

        <div class="p-6 rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-800 text-white flex flex-col justify-between shadow-xs">
            <div>
                <span class="text-xs uppercase font-bold tracking-wider text-indigo-200">Perlu Libur / Izin?</span>
                <h4 class="text-sm font-extrabold mt-1">Ajukan Permohonan Cuti Baru</h4>
            </div>
            <a href="{{ route('employee.leaves.create') }}" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white text-indigo-700 font-bold text-xs shadow hover:bg-indigo-50 transition active:scale-95">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span>Ajukan Cuti Baru</span>
            </a>
        </div>
    </div>

    <!-- Leave Requests History Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Riwayat Pengajuan Cuti Anda</h3>
                <p class="text-xs text-slate-500">Status verifikasi berjenjang (Manager -> HR Final).</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/75 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="py-3 px-6">Jenis Cuti</th>
                        <th class="py-3 px-4">Rentang Tanggal</th>
                        <th class="py-3 px-4">Durasi</th>
                        <th class="py-3 px-4">Alasan</th>
                        <th class="py-3 px-4">Status & Alur</th>
                        <th class="py-3 px-6 text-right">Catatan / Lampiran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($leaves as $item)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-6 font-semibold text-slate-900">
                                <span class="inline-flex items-center gap-1.5 font-bold">
                                    {{ $item->leave_type }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-medium">
                                {{ $item->start_date->translatedFormat('d M Y') }} &ndash; {{ $item->end_date->translatedFormat('d M Y') }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $item->total_days }} Hari
                            </td>
                            <td class="py-3.5 px-4 max-w-xs truncate text-slate-600">
                                {{ $item->reason }}
                            </td>
                            <td class="py-3.5 px-4">
                                <x-status-badge :status="$item->status" />
                                <div class="text-[10px] text-slate-400 mt-1">
                                    @if($item->status === 'Pending Manager')
                                        Menunggu review Manager
                                    @elseif($item->status === 'Pending HR')
                                        Disetujui Manager ({{ $item->managerApprover->name ?? '-' }}), menunggu HR
                                    @elseif($item->status === 'Approved')
                                        Disetujui HR ({{ $item->hrApprover->name ?? '-' }})
                                    @elseif($item->status === 'Rejected')
                                        Ditolak
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                @if($item->rejection_note)
                                    <span class="inline-block text-[11px] text-rose-600 font-semibold bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
                                        {{ $item->rejection_note }}
                                    </span>
                                @elseif($item->attachment_path)
                                    <a href="{{ route('leaves.attachment', $item) }}" target="_blank"
                                       class="inline-flex items-center gap-1 text-[11px] text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-2 py-0.5 rounded border border-indigo-200 transition font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                        <span>Lihat Lampiran</span>
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8">
                                <x-empty-state title="Belum ada pengajuan cuti" description="Anda belum pernah mengajukan permohonan cuti atau izin." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leaves->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $leaves->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
