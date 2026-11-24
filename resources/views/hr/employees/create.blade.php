<x-app-layout title="Tambah Karyawan Baru">
    <x-slot name="breadcrumb">
        <a href="{{ route('hr.dashboard') }}" class="hover:text-indigo-600 transition">HR</a>
        <span>&rsaquo;</span>
        <a href="{{ route('hr.employees.index') }}" class="hover:text-indigo-600 transition">Karyawan</a>
        <span>&rsaquo;</span>
        <span class="text-slate-700">Pendaftaran</span>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('hr.employees.store') }}" method="POST" enctype="multipart/form-data"
              x-data="{ contractType: '{{ old('contract_type', 'PKWTT') }}' }" class="space-y-6">
            @csrf

            <!-- Section 1: Akun & Identitas Pribadi -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                    <span class="w-6 h-6 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-600 flex items-center justify-center font-bold text-xs">1</span>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Identitas & Akun Pengguna</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap Karyawan</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Ahmad Fauzi"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Email Perusahaan (Untuk Login ESS)</label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="e.g. ahmad.fauzi@nashris.test"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nomor Handphone / WhatsApp</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="e.g. 081234567890"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi Akun</label>
                        <input type="text" name="password" value="{{ old('password', 'password') }}" placeholder="Default: password"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-slate-50" />
                        <span class="block text-[10px] text-slate-400 mt-0.5">Biarkan jika ingin menggunakan default <code>password</code>.</span>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Alamat Domisili KTP</label>
                        <textarea name="address" rows="2" required placeholder="Jl. Raya Sudirman No. 12, Kelurahan, Kecamatan..."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none">{{ old('address') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 2: Penugasan Organisasi & Shift -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                    <span class="w-6 h-6 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-600 flex items-center justify-center font-bold text-xs">2</span>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Penempatan Organisasi & Shift Kerja</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Departemen</label>
                        <select name="department_id" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white">
                            <option value="">Pilih Departemen...</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }} ({{ $dept->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Jabatan / Role</label>
                        <select name="position_id" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white">
                            <option value="">Pilih Jabatan...</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos->id }}" {{ old('position_id') == $pos->id ? 'selected' : '' }}>
                                    {{ $pos->title }} ({{ $pos->department->code ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Shift Kerja</label>
                        <select name="shift_id" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white">
                            <option value="">Pilih Shift...</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                    {{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Atasan Langsung (Manager)</label>
                        <select name="manager_id"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white">
                            <option value="">Tanpa Atasan Langsung (Top Head)</option>
                            @foreach($managers as $mgr)
                                <option value="{{ $mgr->id }}" {{ old('manager_id') == $mgr->id ? 'selected' : '' }}>
                                    {{ $mgr->user->name ?? 'Staff' }} - {{ $mgr->department->name ?? '' }} ({{ $mgr->employee_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 3: Status Kontrak & Kompensasi -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                    <span class="w-6 h-6 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-600 flex items-center justify-center font-bold text-xs">3</span>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Status Kontrak & Kompensasi</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tanggal Mulai Bergabung</label>
                        <input type="date" name="join_date" value="{{ old('join_date', date('Y-m-d')) }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tipe Kontrak Kerja</label>
                        <select name="contract_type" x-model="contractType" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white">
                            <option value="PKWTT">PKWTT (Karyawan Tetap)</option>
                            <option value="PKWT">PKWT (Karyawan Kontrak)</option>
                            <option value="Probation">Probation (Masa Percobaan)</option>
                            <option value="Internship">Internship (Magang)</option>
                        </select>
                    </div>

                    <div x-show="contractType !== 'PKWTT'" x-cloak>
                        <label class="block text-xs font-semibold text-amber-800 uppercase tracking-wider mb-1">Tanggal Berakhir Kontrak</label>
                        <input type="date" name="contract_end_date" value="{{ old('contract_end_date') }}"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-amber-300 bg-amber-50/50 text-xs text-slate-900 focus:ring-2 focus:ring-amber-500 focus:outline-none" />
                        <span class="block text-[10px] text-amber-700 mt-0.5">Wajib diisi untuk status non-PKWTT. Digunakan pada widget peringatan 30 hari.</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Gaji Pokok Bulanan (Rp)</label>
                        <input type="number" name="base_salary" value="{{ old('base_salary', 9000000) }}" required min="0" step="100000"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tunjangan Tetap (Rp)</label>
                        <input type="number" name="allowance_fixed" value="{{ old('allowance_fixed', 1000000) }}" required min="0" step="50000"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                    </div>
                </div>
            </div>

            <!-- Section 4: Berkas Dokumen Awal (Opsional) -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                    <span class="w-6 h-6 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-600 flex items-center justify-center font-bold text-xs">4</span>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Berkas Dokumen Digital (Opsional — Maks. 2MB, PDF/JPG/PNG)</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Dokumen KTP</label>
                        <input type="file" name="document_ktp" accept=".pdf,.jpg,.jpeg,.png"
                            class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Dokumen NPWP</label>
                        <input type="file" name="document_npwp" accept=".pdf,.jpg,.jpeg,.png"
                            class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Dokumen CV / Resume</label>
                        <input type="file" name="document_cv" accept=".pdf,.jpg,.jpeg,.png"
                            class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Surat Kontrak / SK</label>
                        <input type="file" name="document_contract" accept=".pdf,.jpg,.jpeg,.png"
                            class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition" />
                    </div>
                </div>
            </div>

            <!-- Submit Action -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('hr.employees.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-md shadow-indigo-600/20 hover:shadow-indigo-600/30 transition">
                    Daftarkan Karyawan & Auto-Generate NIK
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
