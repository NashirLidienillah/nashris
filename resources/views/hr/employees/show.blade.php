<x-app-layout title="Dossier Karyawan">
    <x-slot name="breadcrumb">
        <a href="{{ route('hr.dashboard') }}" class="hover:text-indigo-600 transition">HR</a>
        <span>&rsaquo;</span>
        <a href="{{ route('hr.employees.index') }}" class="hover:text-indigo-600 transition">Karyawan</a>
        <span>&rsaquo;</span>
        <span class="text-slate-700">{{ $employee->employee_code }}</span>
    </x-slot>

    <!-- Top Profile Dossier Card -->
    <div class="p-6 lg:p-8 rounded-2xl bg-white border border-slate-200 shadow-xs mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-700 text-white font-extrabold text-2xl flex items-center justify-center shadow-md shadow-indigo-600/20 flex-shrink-0">
                    {{ strtoupper(substr($employee->user->name ?? '?', 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">{{ $employee->user->name ?? '-' }}</h2>
                        <x-status-badge :status="$employee->contract_type" />
                    </div>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1 text-xs text-slate-500">
                        <span class="font-mono font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200">{{ $employee->employee_code }}</span>
                        <span>{{ $employee->user->email ?? '-' }}</span>
                        <span>&bull;</span>
                        <span>{{ $employee->phone }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2.5">
                <a href="{{ route('hr.employees.edit', $employee) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-300 hover:border-indigo-400 text-xs font-semibold text-slate-700 hover:text-indigo-600 bg-white transition shadow-2xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    <span>Ubah Profil</span>
                </a>

                <form action="{{ route('hr.employees.destroy', $employee) }}" method="POST"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus karyawan ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-xl border border-rose-200 hover:bg-rose-50 text-xs font-semibold text-rose-600 bg-white transition shadow-2xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- 2-Columns Dossier Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left 2 Cols: Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- 1. Data Kepegawaian & Struktur -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 border-b border-slate-100 pb-3">
                    Struktur Organisasi & Penugasan
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="block text-slate-400 mb-0.5">Departemen:</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $employee->department->name ?? '-' }}</span>
                        <span class="block text-[11px] text-slate-500 font-mono">Kode: {{ $employee->department->code ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 mb-0.5">Jabatan / Role:</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $employee->position->title ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 mb-0.5">Jadwal Shift:</span>
                        <span class="font-semibold text-slate-800">{{ $employee->shift->name ?? '-' }}</span>
                        <span class="block text-[11px] text-slate-500 font-mono">
                            {{ $employee->shift->start_time ?? '' }} - {{ $employee->shift->end_time ?? '' }} (Toleransi: {{ $employee->shift->grace_period_minutes ?? 15 }} mnt)
                        </span>
                    </div>

                    <div>
                        <span class="block text-slate-400 mb-0.5">Atasan Langsung:</span>
                        <span class="font-semibold text-slate-800">{{ $employee->manager?->user->name ?? 'Tidak Ada (Top Head)' }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. Kontrak & Kompensasi -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 border-b border-slate-100 pb-3">
                    Kontrak Kerja & Kompensasi
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="block text-slate-400 mb-0.5">Tanggal Bergabung:</span>
                        <span class="font-bold text-slate-900">
                            {{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->translatedFormat('l, d F Y') : '-' }}
                        </span>
                    </div>

                    <div>
                        <span class="block text-slate-400 mb-0.5">Tanggal Berakhir Kontrak:</span>
                        @if($employee->contract_end_date)
                            @php
                                $daysLeft = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($employee->contract_end_date), false);
                            @endphp
                            <span class="font-bold text-amber-800">
                                {{ \Carbon\Carbon::parse($employee->contract_end_date)->translatedFormat('d F Y') }}
                                <span class="text-[11px] font-normal">({{ $daysLeft >= 0 ? $daysLeft . ' hari lagi' : 'Expired' }})</span>
                            </span>
                        @else
                            <span class="font-medium text-emerald-700">Tetap (PKWTT)</span>
                        @endif
                    </div>

                    <div>
                        <span class="block text-slate-400 mb-0.5">Gaji Pokok:</span>
                        <span class="font-mono font-extrabold text-slate-900 text-sm">
                            Rp {{ number_format($employee->base_salary, 0, ',', '.') }}
                        </span>
                    </div>

                    <div>
                        <span class="block text-slate-400 mb-0.5">Tunjangan Tetap:</span>
                        <span class="font-mono font-extrabold text-slate-900 text-sm">
                            Rp {{ number_format($employee->allowance_fixed, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="sm:col-span-2 pt-2 border-t border-slate-100">
                        <span class="block text-slate-400 mb-0.5">Alamat Domisili KTP:</span>
                        <p class="text-slate-800 leading-relaxed">{{ $employee->address }}</p>
                    </div>
                </div>
            </div>

            <!-- 3. Kuota Cuti Karyawan -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 border-b border-slate-100 pb-3">
                    Saldo Cuti Karyawan (Tahun {{ date('Y') }})
                </h3>
                @php
                    $balance = $employee->leaveBalances->firstWhere('year', date('Y'));
                @endphp
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="block text-[11px] font-bold text-slate-400 uppercase">Total Hak</span>
                        <div class="text-xl font-extrabold text-slate-900 mt-1">{{ $balance->total_quota ?? 12 }} Hari</div>
                    </div>
                    <div class="p-4 rounded-xl bg-rose-50/50 border border-rose-100">
                        <span class="block text-[11px] font-bold text-rose-500 uppercase">Terpakai</span>
                        <div class="text-xl font-extrabold text-rose-600 mt-1">{{ $balance->used_quota ?? 0 }} Hari</div>
                    </div>
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100">
                        <span class="block text-[11px] font-bold text-emerald-600 uppercase">Sisa Saldo</span>
                        <div class="text-xl font-extrabold text-emerald-700 mt-1">{{ $balance->remaining_quota ?? 12 }} Hari</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right 1 Col: Document Vault (Manajemen Dokumen Digital) -->
        <div class="space-y-6">
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">📁</span>
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Document Vault</h3>
                    </div>
                    <span class="text-xs font-semibold text-slate-500">{{ $employee->documents->count() }} Berkas</span>
                </div>

                <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                    Dokumen digital resmi karyawan (KTP, NPWP, CV, Surat Kontrak). Seluruh akses dijamin melalui Anti-IDOR Authorization Policy.
                </p>

                <!-- Document List -->
                <div class="space-y-3 mb-6">
                    @forelse($employee->documents as $doc)
                        <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/60 hover:bg-slate-50 transition flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-700 font-bold text-xs flex items-center justify-center">
                                    📄
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-900">{{ $doc->document_type }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $doc->created_at->translatedFormat('d M Y') }}</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <!-- View / Stream Inline -->
                                <a href="{{ route('hr.employees.documents.view', [$employee, $doc]) }}" target="_blank"
                                   title="Pratinjau Dokumen"
                                   class="p-1.5 rounded-lg border border-slate-200 hover:border-indigo-400 text-slate-600 hover:text-indigo-600 bg-white transition">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </a>

                                <!-- Download File -->
                                <a href="{{ route('hr.employees.documents.download', [$employee, $doc]) }}"
                                   title="Unduh Dokumen"
                                   class="p-1.5 rounded-lg border border-slate-200 hover:border-indigo-400 text-slate-600 hover:text-indigo-600 bg-white transition">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                </a>

                                <!-- Delete Document -->
                                <form action="{{ route('hr.employees.documents.destroy', [$employee, $doc]) }}" method="POST"
                                    onsubmit="return confirm('Hapus dokumen {{ $doc->document_type }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus Dokumen"
                                        class="p-1.5 rounded-lg border border-rose-200 hover:bg-rose-50 text-rose-600 bg-white transition">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center rounded-xl bg-slate-50 border border-dashed border-slate-200 text-slate-400 text-xs">
                            Belum ada dokumen digital yang tersimpan.
                        </div>
                    @endforelse
                </div>

                <!-- Upload New Document Form -->
                <div class="pt-4 border-t border-slate-100">
                    <span class="block text-xs font-bold text-slate-900 uppercase tracking-wider mb-2">Unggah Dokumen Baru</span>
                    <form action="{{ route('hr.employees.documents.upload', $employee) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div>
                            <select name="document_type" required
                                class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white">
                                <option value="">Pilih Jenis Dokumen...</option>
                                <option value="KTP">KTP (Identitas)</option>
                                <option value="NPWP">NPWP (Pajak)</option>
                                <option value="CV">CV / Curriculum Vitae</option>
                                <option value="CONTRACT">Surat Perjanjian Kontrak</option>
                            </select>
                        </div>

                        <div>
                            <input type="file" name="document_file" required accept=".pdf,.jpg,.jpeg,.png"
                                class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition" />
                            <span class="block text-[10px] text-slate-400 mt-1">Maks. 2MB (Format: PDF, JPG, PNG).</span>
                        </div>

                        <button type="submit" class="w-full py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-xs transition">
                            Unggah Dokumen
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
