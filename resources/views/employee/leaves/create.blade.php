<x-app-layout title="Form Pengajuan Cuti Baru">
    <x-slot name="breadcrumb">
        <a href="{{ route('employee.dashboard') }}" class="hover:text-slate-600 transition">Portal ESS</a>
        <span>&rsaquo;</span>
        <a href="{{ route('employee.leaves.index') }}" class="hover:text-slate-600 transition">Manajemen Cuti</a>
        <span>&rsaquo;</span>
        <span class="text-slate-700">Form Pengajuan</span>
    </x-slot>

    <div class="max-w-3xl mx-auto" x-data="{
        leaveType: 'Annual',
        startDate: '',
        endDate: '',
        calculateDays() {
            if (!this.startDate || !this.endDate) return 0;
            const start = new Date(this.startDate);
            const end = new Date(this.endDate);
            if (end < start) return 0;
            const diffTime = Math.abs(end - start);
            return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        }
    }">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Formulir Pengajuan Cuti / Izin Kerja</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Permohonan akan diverifikasi berjenjang oleh Manager divisi Anda terlebih dahulu.</p>
                </div>
                <div class="text-right">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Sisa Saldo Cuti</span>
                    <span class="text-sm font-extrabold text-emerald-700 font-mono">{{ $leaveBalance->remaining_quota }} Hari</span>
                </div>
            </div>

            <form action="{{ route('employee.leaves.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf

                <!-- Jenis Cuti -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Jenis Cuti / Izin <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="relative flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition"
                               :class="leaveType === 'Annual' ? 'border-indigo-600 bg-indigo-50/40 text-indigo-900' : 'border-slate-200 hover:bg-slate-50 text-slate-700'">
                            <input type="radio" name="leave_type" value="Annual" x-model="leaveType" class="text-indigo-600 focus:ring-indigo-500">
                            <div>
                                <span class="block text-xs font-bold">Cuti Tahunan (Annual)</span>
                                <span class="block text-[11px] text-slate-500">Memotong saldo kuota tahunan</span>
                            </div>
                        </label>

                        <label class="relative flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition"
                               :class="leaveType === 'Sick' ? 'border-indigo-600 bg-indigo-50/40 text-indigo-900' : 'border-slate-200 hover:bg-slate-50 text-slate-700'">
                            <input type="radio" name="leave_type" value="Sick" x-model="leaveType" class="text-indigo-600 focus:ring-indigo-500">
                            <div>
                                <span class="block text-xs font-bold">Izin Sakit (Sick)</span>
                                <span class="block text-[11px] text-slate-500">Lampirkan surat dokter</span>
                            </div>
                        </label>

                        <label class="relative flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition"
                               :class="leaveType === 'Special' ? 'border-indigo-600 bg-indigo-50/40 text-indigo-900' : 'border-slate-200 hover:bg-slate-50 text-slate-700'">
                            <input type="radio" name="leave_type" value="Special" x-model="leaveType" class="text-indigo-600 focus:ring-indigo-500">
                            <div>
                                <span class="block text-xs font-bold">Cuti Khusus (Special)</span>
                                <span class="block text-[11px] text-slate-500">Menikah, duka, melahirkan</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Date Range Selection -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Tanggal Mulai <span class="text-rose-500">*</span></label>
                        <input type="date" id="start_date" name="start_date" x-model="startDate" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="end_date" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Tanggal Selesai <span class="text-rose-500">*</span></label>
                        <input type="date" id="end_date" name="end_date" x-model="endDate" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Duration Preview Box -->
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <span class="text-xs text-slate-600 font-semibold">Total Durasi Hari Cuti:</span>
                    <span class="text-base font-extrabold text-indigo-700" x-text="calculateDays() + ' Hari Kalender'">0 Hari Kalender</span>
                </div>

                <!-- Reason -->
                <div>
                    <label for="reason" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Alasan Pengajuan <span class="text-rose-500">*</span></label>
                    <textarea id="reason" name="reason" rows="3" required placeholder="Jelaskan keperluan cuti atau izin Anda..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <!-- Attachment Upload -->
                <div>
                    <label for="attachment" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Lampiran Dokumen Pendukung <span class="text-slate-400 font-normal">(Opsional / Surat Dokter jika sakit)</span>
                    </label>
                    <input type="file" id="attachment" name="attachment" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <span class="text-[11px] text-slate-400 mt-1 block">Format: PDF, JPG, PNG (Maksimal 2MB).</span>
                </div>

                <!-- Submit Button Bar -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('employee.leaves.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition active:scale-95">
                        Kirim Pengajuan Cuti
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
