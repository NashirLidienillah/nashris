<x-app-layout title="Persetujuan Cuti Tim (Review Tahap 1)">
    <x-slot name="breadcrumb">
        <a href="{{ route('manager.dashboard') }}" class="hover:text-slate-600 transition">Manager Portal</a>
        <span>&rsaquo;</span>
        <span class="text-slate-700">Approval Cuti Tim</span>
    </x-slot>

    <!-- Header Card -->
    <div class="mb-6 p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200">
                Departemen {{ $department->name }}
            </span>
            <h2 class="text-lg font-bold text-slate-900 mt-1">Review Pengajuan Cuti Anggota Divisi</h2>
            <p class="text-xs text-slate-500">Persetujuan tahap 1 sebelum diteruskan ke HR untuk verifikasi akhir dan pemotongan kuota.</p>
        </div>

        <!-- Filter Status Tab -->
        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl text-xs font-semibold">
            <a href="{{ route('manager.leaves.index', ['status' => 'Pending Manager']) }}"
               class="px-3 py-1.5 rounded-lg transition {{ request('status', 'Pending Manager') === 'Pending Manager' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                Menunggu Review
            </a>
            <a href="{{ route('manager.leaves.index', ['status' => 'all']) }}"
               class="px-3 py-1.5 rounded-lg transition {{ request('status') === 'all' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                Semua Riwayat
            </a>
        </div>
    </div>

    <!-- Table of Team Leave Requests -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden" x-data="{ rejectModalOpen: false, selectedLeaveId: null, employeeName: '' }">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/75 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="py-3 px-6">Nama Karyawan</th>
                        <th class="py-3 px-4">Jenis Cuti</th>
                        <th class="py-3 px-4">Rentang Tanggal</th>
                        <th class="py-3 px-4">Durasi</th>
                        <th class="py-3 px-4">Alasan</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-6 text-right">Aksi Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($leaves as $item)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-6 font-medium text-slate-900">
                                <div class="font-bold">{{ $item->employee->user->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $item->employee->employee_code }} &bull; {{ $item->employee->position->title ?? '-' }}</div>
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
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                @if($item->status === 'Pending Manager')
                                    <div class="inline-flex items-center gap-2">
                                        <form action="{{ route('manager.leaves.approve', $item) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition active:scale-95">
                                                Setujui
                                            </button>
                                        </form>

                                        <button type="button"
                                                @click="rejectModalOpen = true; selectedLeaveId = {{ $item->id }}; employeeName = @js($item->employee->user->name ?? '')"
                                                class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 font-bold text-xs transition">
                                            Tolak
                                        </button>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-[11px]">Telah direview</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8">
                                <x-empty-state title="Tidak ada pengajuan cuti" description="Tidak ada permohonan cuti anggota tim yang menunggu review saat ini." />
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

        <!-- Rejection Reason Modal (Alpine.js) -->
        <div x-show="rejectModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div @click.away="rejectModalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200">
                <h3 class="text-base font-bold text-slate-900 mb-1">Tolak Pengajuan Cuti</h3>
                <p class="text-xs text-slate-500 mb-4">Berikan catatan alasan penolakan permohonan cuti untuk <strong x-text="employeeName"></strong>.</p>

                <form :action="'{{ url('/manager/leaves') }}/' + selectedLeaveId + '/reject'" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="reject">

                    <div class="mb-4">
                        <label for="rejection_note" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Alasan Penolakan <span class="text-rose-500">*</span></label>
                        <textarea id="rejection_note" name="rejection_note" rows="3" required placeholder="Tuliskan alasan penolakan pengajuan cuti..."
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
