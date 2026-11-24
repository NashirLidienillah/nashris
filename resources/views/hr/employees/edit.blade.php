<x-app-layout title="Ubah Data Karyawan">
    <x-slot name="breadcrumb">
        <a href="{{ route('hr.dashboard') }}" class="hover:text-indigo-600 transition">HR</a>
        <span>&rsaquo;</span>
        <a href="{{ route('hr.employees.index') }}" class="hover:text-indigo-600 transition">Karyawan</a>
        <span>&rsaquo;</span>
        <a href="{{ route('hr.employees.show', $employee) }}" class="hover:text-indigo-600 transition">{{ $employee->employee_code }}</a>
        <span>&rsaquo;</span>
        <span class="text-slate-700">Ubah Data</span>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('hr.employees.update', $employee) }}" method="POST"
              x-data="{ contractType: '{{ old('contract_type', $employee->contract_type) }}' }" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1: Akun & Identitas Pribadi -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-600 flex items-center justify-center font-bold text-xs">1</span>
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Identitas & Akun</h3>
                    </div>
                    <span class="font-mono text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-200">
                        {{ $employee->employee_code }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap Karyawan</label>
                        <input type="text" name="name" value="{{ old('name', $employee->user->name) }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Email Perusahaan</label>
                        <input type="email" name="email" value="{{ old('email', $employee->user->email) }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nomor Handphone / WhatsApp</label>
                        <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Peran Akun Sistem (Role)</label>
                        <select name="role" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 font-semibold focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white">
                            @php
                                $userRole = old('role', $employee->user->roles->first()?->name ?? 'employee');
                            @endphp
                            <option value="employee" {{ $userRole === 'employee' ? 'selected' : '' }}>Employee (Staf Biasa / Akses ESS)</option>
                            <option value="manager" {{ $userRole === 'manager' ? 'selected' : '' }}>Manager (Supervisi Divisi / Approval Cuti & Presensi)</option>
                            <option value="hr" {{ $userRole === 'hr' ? 'selected' : '' }}>HR Administrator (Akses Penuh)</option>
                        </select>
                        <span class="block text-[10px] text-slate-400 mt-0.5">Ubah ke <strong>Manager</strong> untuk mengangkat karyawan menjadi supervisor yang dapat mereview cuti timnya.</span>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">NIK Karyawan (Permanen)</label>
                        <input type="text" value="{{ $employee->employee_code }}" disabled
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-500 font-mono bg-slate-100 cursor-not-allowed" />
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Alamat Domisili KTP</label>
                        <textarea name="address" rows="2" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none">{{ old('address', $employee->address) }}</textarea>
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
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }} ({{ $dept->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Jabatan / Role</label>
                        <select name="position_id" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white">
                            @foreach($positions as $pos)
                                <option value="{{ $pos->id }}" {{ old('position_id', $employee->position_id) == $pos->id ? 'selected' : '' }}>
                                    {{ $pos->title }} ({{ $pos->department->code ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Shift Kerja</label>
                        <select name="shift_id" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white">
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" {{ old('shift_id', $employee->shift_id) == $shift->id ? 'selected' : '' }}>
                                    {{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Atasan Langsung (Manager)</label>
                        <select name="manager_id"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-white">
                            <option value="">Tanpa Atasan Langsung</option>
                            @foreach($managers as $mgr)
                                <option value="{{ $mgr->id }}" {{ old('manager_id', $employee->manager_id) == $mgr->id ? 'selected' : '' }}>
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
                        <input type="date" name="join_date" value="{{ old('join_date', $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('Y-m-d') : '') }}" required
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
                        <input type="date" name="contract_end_date" value="{{ old('contract_end_date', $employee->contract_end_date ? \Carbon\Carbon::parse($employee->contract_end_date)->format('Y-m-d') : '') }}"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-amber-300 bg-amber-50/50 text-xs text-slate-900 focus:ring-2 focus:ring-amber-500 focus:outline-none" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Gaji Pokok Bulanan (Rp)</label>
                        <input type="number" name="base_salary" value="{{ old('base_salary', (int)$employee->base_salary) }}" required min="0" step="100000"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tunjangan Tetap (Rp)</label>
                        <input type="number" name="allowance_fixed" value="{{ old('allowance_fixed', (int)$employee->allowance_fixed) }}" required min="0" step="50000"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                    </div>
                </div>
            </div>

            <!-- Submit Action -->
            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-2">
                <a href="{{ route('hr.employees.show', $employee) }}" class="px-5 py-2.5 min-h-[44px] flex items-center justify-center rounded-xl border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 min-h-[44px] flex items-center justify-center rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-md shadow-indigo-600/20 hover:shadow-indigo-600/30 transition">
                    Simpan Perubahan Data
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
