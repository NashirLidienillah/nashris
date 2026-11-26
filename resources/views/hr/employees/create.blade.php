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
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Peran Akun Sistem (Role)</label>
                        <x-custom-select
                            name="role"
                            :options="[
                                'employee' => 'Employee (Staf Biasa / Akses ESS)',
                                'manager' => 'Manager (Supervisi Divisi / Approval Cuti & Presensi)',
                                'hr' => 'HR Administrator (Akses Penuh)'
                            ]"
                            :value="old('role', 'employee')"
                            required
                        />
                        <span class="block text-[10px] text-slate-400 mt-1">Pilih <strong>Manager</strong> jika karyawan ini bertindak sebagai supervisor tim atau kepala departemen.</span>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi Sementara (Opsional)</label>
                        <input type="text" name="password" value="{{ old('password') }}" placeholder="Kosongkan untuk generate otomatis"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-slate-50" />
                        <span class="block text-[10px] text-slate-400 mt-0.5">Jika dikosongkan, sistem akan membuat password sementara acak yang dapat Anda salin. Karyawan wajib menggantinya saat login pertama.</span>
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
                        <x-custom-select
                            name="department_id"
                            :options="$departments->map(fn($d) => ['value' => $d->id, 'label' => $d->name . ' (' . $d->code . ')'])"
                            placeholder="Pilih Departemen..."
                            :value="old('department_id')"
                            required
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Jabatan / Role</label>
                        <x-custom-select
                            name="position_id"
                            :options="$positions->map(fn($p) => ['value' => $p->id, 'label' => $p->title . ' (' . ($p->department->code ?? '-') . ')'])"
                            placeholder="Pilih Jabatan..."
                            :value="old('position_id')"
                            required
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Shift Kerja</label>
                        <x-custom-select
                            name="shift_id"
                            :options="$shifts->map(fn($s) => ['value' => $s->id, 'label' => $s->name . ' (' . $s->start_time . ' - ' . $s->end_time . ')'])"
                            placeholder="Pilih Shift..."
                            :value="old('shift_id')"
                            required
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Atasan Langsung (Manager)</label>
                        <x-custom-select
                            name="manager_id"
                            :options="$managers->map(fn($m) => ['value' => $m->id, 'label' => ($m->user->name ?? 'Staff') . ' - ' . ($m->department->name ?? '') . ' (' . $m->employee_code . ')'])"
                            placeholder="Tanpa Atasan Langsung (Top Head)"
                            :value="old('manager_id')"
                        />
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
                        <x-custom-select
                            name="contract_type"
                            :options="[
                                'PKWTT' => 'PKWTT (Karyawan Tetap)',
                                'PKWT' => 'PKWT (Karyawan Kontrak)',
                                'Probation' => 'Probation (Masa Percobaan)',
                                'Internship' => 'Internship (Magang)'
                            ]"
                            x-model="contractType"
                            required
                        />
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
            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-2">
                <a href="{{ route('hr.employees.index') }}" class="px-5 py-2.5 min-h-[44px] flex items-center justify-center rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 min-h-[44px] flex items-center justify-center rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-md shadow-indigo-600/20 hover:shadow-indigo-600/30 transition">
                    Daftarkan Karyawan & Auto-Generate NIK
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
