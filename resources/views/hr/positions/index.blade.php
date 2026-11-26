<x-app-layout title="Kelola Jabatan & Level">
    <x-slot name="breadcrumb">
        <a href="{{ route('hr.dashboard') }}" class="hover:text-indigo-600 transition">HR</a>
        <span>&rsaquo;</span>
        <span class="text-slate-700">Jabatan</span>
    </x-slot>

    <!-- Header Filter & Tambah Jabatan -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4" x-data="{
        editPosOpen: false,
        activePos: { id: '', department_id: '', title: '', base_salary_default: 0 },
        openEdit(pos) {
            this.activePos = Object.assign({}, pos);
            this.editPosOpen = true;
        }
    }">
        <!-- Department & Keyword Filters -->
        <form method="GET" action="{{ route('hr.positions.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="w-56">
                <x-custom-select name="department_id" :options="$departments->map(fn($d) => ['value' => $d->id, 'label' => $d->name . ' (' . $d->code . ')'])" placeholder="Semua Departemen" :value="request('department_id')" :submitOnChange="true" size="sm" />
            </div>

            <div class="relative flex-1 max-w-xs">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama jabatan..."
                    class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-2xs bg-white" />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>

            @if(request()->hasAny(['department_id', 'search']))
                <a href="{{ route('hr.positions.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-800 font-medium">Reset</a>
            @endif
        </form>

        <!-- Tambah Jabatan Trigger -->
        <button type="button" @click="$dispatch('open-modal', 'add-position-modal')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold text-xs shadow-md shadow-indigo-600/20 hover:shadow-indigo-600/30 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            <span>Tambah Jabatan</span>
        </button>

        <!-- Edit Modal Alpine -->
        <div x-show="editPosOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="editPosOpen = false"></div>
            <div class="flex min-h-full items-center justify-center p-3 sm:p-4">
                <div class="w-full max-w-lg bg-white rounded-2xl border border-slate-200 shadow-2xl overflow-hidden relative max-h-[90vh] flex flex-col">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/60 flex-shrink-0">
                        <h3 class="text-base font-bold text-slate-900">Ubah Data Jabatan</h3>
                        <button type="button" @click="editPosOpen = false" aria-label="Tutup" class="text-slate-400 hover:text-slate-600 p-2 min-h-[44px] min-w-[44px] flex items-center justify-center rounded-lg">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form :action="'{{ url('hr/positions') }}/' + activePos.id" method="POST" class="p-6 space-y-4 overflow-y-auto">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Departemen</label>
                            <x-custom-select name="department_id" :options="$departments->map(fn($d) => ['value' => $d->id, 'label' => $d->name . ' (' . $d->code . ')'])" x-model="activePos.department_id" required />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Jabatan</label>
                            <input type="text" name="title" x-model="activePos.title" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Standar Gaji Pokok Default (Rp)</label>
                            <input type="number" name="base_salary_default" x-model="activePos.base_salary_default" required min="0" step="100000"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                        </div>

                        <div class="pt-3 flex justify-end gap-2.5">
                            <button type="button" @click="editPosOpen = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm transition">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Positions Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden" x-data>
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold whitespace-nowrap">
                    <tr>
                        <th class="py-3.5 px-6">Jabatan / Role</th>
                        <th class="py-3.5 px-6">Departemen</th>
                        <th class="py-3.5 px-6">Gaji Pokok Default</th>
                        <th class="py-3.5 px-6 text-center">Jumlah Karyawan</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($positions as $pos)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-4 px-6 font-bold text-slate-900 text-sm">
                                {{ $pos->title }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    {{ $pos->department->name ?? '-' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 font-mono font-bold text-slate-900">
                                Rp {{ number_format($pos->base_salary_default, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                    {{ $pos->employees_count }} Orang
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <button type="button" @click="$dispatch('edit-pos', {{ json_encode($pos) }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 hover:border-indigo-400 text-xs font-semibold text-slate-700 hover:text-indigo-600 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    <span>Ubah</span>
                                </button>

                                <form action="{{ route('hr.positions.destroy', $pos) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        @click="$dispatch('open-confirm', {
                                            title: 'Hapus Jabatan',
                                            message: 'Apakah Anda yakin ingin menghapus jabatan {{ $pos->title }}? Jabatan tidak dapat dihapus jika sedang diemban oleh karyawan aktif.',
                                            confirmText: 'Ya, Hapus',
                                            type: 'danger',
                                            form: $el.closest('form')
                                        })"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-rose-200 hover:bg-rose-50 text-xs font-semibold text-rose-600 transition">
                                        <svg class="w-3.5 h-3.5 stroke-[1.75]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400">
                                Tidak ada data jabatan yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($positions->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $positions->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Jabatan -->
    <x-modal name="add-position-modal" title="Tambah Jabatan Baru" maxWidth="lg">
        <form action="{{ route('hr.positions.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Departemen Terkait</label>
                <x-custom-select name="department_id" :options="$departments->map(fn($d) => ['value' => $d->id, 'label' => $d->name . ' (' . $d->code . ')'])" placeholder="Pilih Departemen..." :value="old('department_id')" required />
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Jabatan</label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="e.g. Senior Software Engineer"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Standar Gaji Pokok Default (Rp)</label>
                <input type="number" name="base_salary_default" value="{{ old('base_salary_default', 8000000) }}" required min="0" step="100000"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
            </div>

            <div class="pt-3 flex justify-end gap-2.5">
                <button type="button" @click="$dispatch('close-modal', 'add-position-modal')" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm transition">
                    Simpan Jabatan
                </button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
