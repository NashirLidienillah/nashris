<x-app-layout title="Direktori Karyawan">
    <x-slot name="breadcrumb">
        <a href="{{ route('hr.dashboard') }}" class="hover:text-indigo-600 transition">HR</a>
        <span>&rsaquo;</span>
        <span class="text-slate-700">Direktori Karyawan</span>
    </x-slot>

    <!-- Header & Filter Bar -->
    <div class="mb-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('hr.employees.index') }}" class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-2.5 flex-1">
            <!-- Keyword Search -->
            <div class="relative min-w-0 sm:min-w-[220px] flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama, NIK, atau email..."
                    class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-2xs bg-white min-h-[44px]" />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>

            <!-- Department Filter -->
            <div class="w-full sm:w-48">
                <x-custom-select
                    name="department_id"
                    :options="$departments"
                    valueKey="id"
                    labelKey="name"
                    placeholder="Semua Departemen"
                    :value="request('department_id')"
                    :submitOnChange="true"
                    size="sm"
                />
            </div>

            <!-- Contract Filter -->
            <div class="w-full sm:w-48">
                <x-custom-select
                    name="contract_type"
                    :options="$contractTypes"
                    placeholder="Semua Status Kontrak"
                    :value="request('contract_type')"
                    :submitOnChange="true"
                    size="sm"
                />
            </div>

            <!-- Role Filter -->
            <div class="w-full sm:w-40">
                <x-custom-select
                    name="role"
                    :options="[
                        'manager' => 'Manager',
                        'employee' => 'Employee',
                        'hr' => 'HR Admin'
                    ]"
                    placeholder="Semua Role"
                    :value="request('role')"
                    :submitOnChange="true"
                    size="sm"
                />
            </div>

            @if(request()->hasAny(['department_id', 'contract_type', 'role', 'search']))
                <a href="{{ route('hr.employees.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-800 font-medium self-center">Reset</a>
            @endif
        </form>

        <!-- Tambah Karyawan Button -->
        <a href="{{ route('hr.employees.create') }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 min-h-[44px] rounded-xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold text-xs shadow-md shadow-indigo-600/20 hover:shadow-indigo-600/30 transition flex-shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
            <span>Tambah Karyawan Baru</span>
        </a>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold whitespace-nowrap">
                    <tr>
                        <th class="py-3.5 px-6">Identitas Karyawan</th>
                        <th class="py-3.5 px-4">Role Sistem</th>
                        <th class="py-3.5 px-6">Departemen & Jabatan</th>
                        <th class="py-3.5 px-4">Shift Kerja</th>
                        <th class="py-3.5 px-4">Status Kontrak</th>
                        <th class="py-3.5 px-4">Gaji Pokok</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($employees as $emp)
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Identitas -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center flex-shrink-0 border border-indigo-200">
                                        {{ strtoupper(substr($emp->user->name ?? '?', 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('hr.employees.show', $emp) }}" class="font-bold text-slate-900 text-sm hover:text-indigo-600 transition">
                                            {{ $emp->user->name ?? '-' }}
                                        </a>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="font-mono text-[11px] font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 px-1.5 py-0.2 rounded">{{ $emp->employee_code }}</span>
                                            <span class="text-[11px] text-slate-400">{{ $emp->user->email ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Role Sistem -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                @php
                                    $roleName = $emp->user->roles->first()?->name ?? 'employee';
                                @endphp
                                @if($roleName === 'hr')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                        HR Admin
                                    </span>
                                @elseif($roleName === 'manager')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Manager
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-sky-50 text-sky-700 border border-sky-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                        Employee
                                    </span>
                                @endif
                            </td>

                            <!-- Departemen & Jabatan -->
                            <td class="py-4 px-6">
                                <div class="font-semibold text-slate-900">{{ $emp->department->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $emp->position->title ?? '-' }}</div>
                            </td>

                            <!-- Shift -->
                            <td class="py-4 px-4 font-medium text-slate-700">
                                <div>{{ $emp->shift->name ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $emp->shift->start_time ?? '' }} - {{ $emp->shift->end_time ?? '' }}</div>
                            </td>

                            <!-- Status Kontrak -->
                            <td class="py-4 px-4">
                                <x-status-badge :status="$emp->contract_type" />
                                @if($emp->contract_end_date)
                                    <div class="text-[10px] text-slate-400 mt-1">
                                        s/d {{ \Carbon\Carbon::parse($emp->contract_end_date)->translatedFormat('d M Y') }}
                                    </div>
                                @endif
                            </td>

                            <!-- Gaji Pokok -->
                            <td class="py-4 px-4 font-mono font-bold text-slate-900">
                                Rp {{ number_format($emp->base_salary, 0, ',', '.') }}
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-6 text-right space-x-1.5 whitespace-nowrap">
                                <a href="{{ route('hr.employees.show', $emp) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 hover:border-indigo-400 text-xs font-semibold text-slate-700 hover:text-indigo-600 bg-white transition shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    <span>Detail</span>
                                </a>

                                <a href="{{ route('hr.employees.edit', $emp) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 hover:border-indigo-400 text-xs font-semibold text-slate-700 hover:text-indigo-600 bg-white transition shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    <span>Ubah</span>
                                </a>

                                <form action="{{ route('hr.employees.destroy', $emp) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        @click="$dispatch('open-confirm', {
                                            title: 'Hapus Karyawan',
                                            message: 'Apakah Anda yakin ingin menghapus karyawan {{ $emp->user->name }} ({{ $emp->employee_code }})? Data akun pengguna dan berkas arsip akan dihapus secara permanen.',
                                            confirmText: 'Ya, Hapus Karyawan',
                                            type: 'danger',
                                            form: $el.closest('form')
                                        })"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-rose-200 hover:bg-rose-50 text-xs font-semibold text-rose-600 bg-white transition shadow-2xs">
                                        <svg class="w-3.5 h-3.5 stroke-[1.75]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <x-empty-state title="Karyawan tidak ditemukan" description="Tidak ada data karyawan yang sesuai dengan kriteria filter atau pencarian Anda." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
