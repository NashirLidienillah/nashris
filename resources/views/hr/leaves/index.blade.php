<x-app-layout title="Manajemen & Persetujuan Akhir Cuti (HR)">
    <x-slot name="breadcrumb">
        <a href="{{ route('hr.dashboard') }}" class="hover:text-slate-600 transition">HR Portal</a>
        <span>&rsaquo;</span>
        <span class="text-slate-700">Persetujuan Cuti</span>
    </x-slot>

    <!-- Header & Filter Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs mb-6">
        <form method="GET" action="{{ route('hr.leaves.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end text-xs">
            <div>
                <label for="status" class="block font-bold text-slate-700 mb-1">Status Pengajuan</label>
                <x-custom-select
                    name="status"
                    id="status"
                    :options="[
                        'Pending HR' => 'Pending HR (Menunggu Verifikasi Akhir)',
                        'Approved' => 'Approved (Disetujui)',
                        'Rejected' => 'Rejected (Ditolak)',
                        'Pending Manager' => 'Pending Manager (Menunggu Tahap 1)',
                        'all' => 'Semua Status'
                    ]"
                    :value="request('status', 'Pending HR')"
                    size="sm"
                />
            </div>

            <div>
                <label for="department_id" class="block font-bold text-slate-700 mb-1">Departemen</label>
                <x-custom-select
                    name="department_id"
                    id="department_id"
                    :options="$departments->map(fn($d) => ['value' => $d->id, 'label' => $d->name . ' (' . $d->code . ')'])"
                    placeholder="Semua Departemen"
                    :value="request('department_id')"
                    size="sm"
                />
            </div>

            <div>
                <label for="leave_type" class="block font-bold text-slate-700 mb-1">Jenis Cuti</label>
                <x-custom-select
                    name="leave_type"
                    id="leave_type"
                    :options="[
                        'Annual' => 'Annual (Tahunan)',
                        'Sick' => 'Sick (Sakit)',
                        'Special' => 'Special (Khusus)'
                    ]"
                    placeholder="Semua Jenis"
                    :value="request('leave_type')"
                    size="sm"
                />
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('hr.leaves.index') }}" class="w-1/2 py-2 text-center rounded-xl border border-slate-300 text-slate-600 font-semibold hover:bg-slate-50 transition">
                    Reset
                </a>
                <button type="submit" class="w-1/2 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold transition shadow-xs">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Master Leave Table & Reject Modal -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden" x-data="{ rejectModalOpen: false, selectedLeaveId: null, employeeName: '' }">
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/75 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-semibold whitespace-nowrap">
                    <tr>
                        <th class="py-3 px-6">Karyawan</th>
                        <th class="py-3 px-4">Departemen</th>
                        <th class="py-3 px-4">Sisa Kuota</th>
                        <th class="py-3 px-4">Jenis Cuti</th>
                        <th class="py-3 px-4">Rentang Tanggal</th>
                        <th class="py-3 px-4">Durasi</th>
                        <th class="py-3 px-4">Alasan</th>
                        <th class="py-3 px-4">Status & Reviewer</th>
                        <th class="py-3 px-6 text-right">Aksi HR</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($leaves as $item)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-6 font-medium text-slate-900">
                                <div class="font-bold">{{ $item->employee->user->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $item->employee->employee_code }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-800">
                                {{ $item->employee->department->name ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-emerald-700">
                                {{ $item->employee->currentLeaveBalance->remaining_quota ?? '-' }} Hari
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-800">
                                {{ $item->leave_type }}
                            </td>
                            <td class="py-3.5 px-4">
                                {{ $item->start_date->translatedFormat('d M Y') }} &ndash; {{ $item->end_date->translatedFormat('d M Y') }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $item->total_days }} Hari
                            </td>
                            <td class="py-3.5 px-4 max-w-xs text-slate-600">
                                <div>{{ $item->reason }}</div>
                                @if($item->attachment_path)
                                    <div class="mt-1">
                                        <a href="{{ route('leaves.attachment', $item) }}" target="_blank"
                                           class="inline-flex items-center gap-1 text-[11px] text-indigo-600 hover:text-indigo-800 font-semibold underline">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                            <span>Lihat Lampiran</span>
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <x-status-badge :status="$item->status" />
                                <div class="text-[10px] text-slate-400 mt-1">
                                    @if($item->managerApprover)
                                        Mgr: {{ $item->managerApprover->name }}
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                @if($item->status === 'Pending HR')
                                    <div class="inline-flex items-center gap-1.5">
                                        <form action="{{ route('hr.leaves.approve', $item) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" title="Verifikasi & Setujui Final"
                                                    class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition active:scale-95">
                                                Setujui Final
                                            </button>
                                        </form>

                                        <button type="button"
                                                @click="rejectModalOpen = true; selectedLeaveId = {{ $item->id }}; employeeName = @js($item->employee->user->name ?? '')"
                                                class="px-2.5 py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 font-bold text-xs transition">
                                            Tolak
                                        </button>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-[11px]">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8">
                                <x-empty-state title="Tidak ada pengajuan cuti" description="Tidak ada permohonan cuti yang membutuhkan verifikasi HR saat ini." />
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

        <!-- HR Rejection Modal -->
        <div x-show="rejectModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-sm">
            <div @click.away="rejectModalOpen = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Tolak Pengajuan Cuti (HR)</h3>
                    <button type="button" @click="rejectModalOpen = false" aria-label="Tutup" class="text-slate-400 hover:text-slate-600 p-2 min-h-[44px] min-w-[44px] flex items-center justify-center rounded-lg">&times;</button>
                </div>
                <p class="text-xs text-slate-500 mb-4">Catatan penolakan untuk <strong x-text="employeeName"></strong>:</p>

                <form :action="'{{ url('/hr/leaves') }}/' + selectedLeaveId + '/reject'" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="reject">

                    <div class="mb-4">
                        <label for="hr_rejection_note" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Alasan Penolakan <span class="text-rose-500">*</span></label>
                        <textarea id="hr_rejection_note" name="rejection_note" rows="3" required placeholder="Tuliskan alasan penolakan HR..."
                                  class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <button type="button" @click="rejectModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs">
                            Konfirmasi Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
