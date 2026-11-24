<x-app-layout title="Kelola Departemen">
    <x-slot name="breadcrumb">
        <a href="{{ route('hr.dashboard') }}" class="hover:text-indigo-600 transition">HR</a>
        <span>&rsaquo;</span>
        <span class="text-slate-700">Departemen</span>
    </x-slot>

    <!-- Header Actions & Search -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4" x-data="{
        editModalOpen: false,
        activeDept: { id: '', name: '', code: '', description: '' },
        openEdit(dept) {
            this.activeDept = Object.assign({}, dept);
            this.editModalOpen = true;
        }
    }">
        <!-- Search & Filter Form -->
        <form method="GET" action="{{ route('hr.departments.index') }}" class="flex-1 max-w-md">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari kode atau nama departemen..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-2xs bg-white" />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>
        </form>

        <!-- Tambah Departemen Trigger -->
        <button type="button" @click="$dispatch('open-modal', 'add-department-modal')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold text-xs shadow-md shadow-indigo-600/20 hover:shadow-indigo-600/30 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            <span>Tambah Departemen</span>
        </button>

        <!-- Edit Modal Alpine -->
        <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="editModalOpen = false"></div>
            <div class="flex min-h-full items-center justify-center p-3 sm:p-4">
                <div class="w-full max-w-lg bg-white rounded-2xl border border-slate-200 shadow-2xl overflow-hidden relative max-h-[90vh] flex flex-col">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/60 flex-shrink-0">
                        <h3 class="text-base font-bold text-slate-900">Ubah Data Departemen</h3>
                        <button type="button" @click="editModalOpen = false" aria-label="Tutup" class="text-slate-400 hover:text-slate-600 p-2 min-h-[44px] min-w-[44px] flex items-center justify-center rounded-lg">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form :action="'{{ url('hr/departments') }}/' + activeDept.id" method="POST" class="p-6 space-y-4 overflow-y-auto">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kode Departemen</label>
                            <input type="text" name="code" x-model="activeDept.code" required maxlength="10"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 uppercase font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Departemen</label>
                            <input type="text" name="name" x-model="activeDept.name" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Operasional</label>
                            <textarea name="description" x-model="activeDept.description" rows="3"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                        </div>

                        <div class="pt-3 flex justify-end gap-2.5">
                            <button type="button" @click="editModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">
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

    <!-- Data Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden" x-data>
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold whitespace-nowrap">
                    <tr>
                        <th class="py-3.5 px-6">Kode</th>
                        <th class="py-3.5 px-6">Nama Departemen</th>
                        <th class="py-3.5 px-6">Deskripsi</th>
                        <th class="py-3.5 px-6 text-center">Jumlah Jabatan</th>
                        <th class="py-3.5 px-6 text-center">Jumlah Karyawan</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($departments as $dept)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-4 px-6 font-mono font-bold text-indigo-600">
                                <span class="bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded-md">{{ $dept->code }}</span>
                            </td>
                            <td class="py-4 px-6 font-bold text-slate-900 text-sm">
                                {{ $dept->name }}
                            </td>
                            <td class="py-4 px-6 max-w-xs truncate text-slate-500">
                                {{ $dept->description ?: '-' }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                    {{ $dept->positions_count }} Jabatan
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    {{ $dept->employees_count }} Orang
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <button type="button" @click="$dispatch('edit-dept', {{ json_encode($dept) }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 hover:border-indigo-400 text-xs font-semibold text-slate-700 hover:text-indigo-600 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    <span>Ubah</span>
                                </button>

                                <form action="{{ route('hr.departments.destroy', $dept) }}" method="POST" class="inline-block"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus departemen {{ $dept->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-rose-200 hover:bg-rose-50 text-xs font-semibold text-rose-600 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">
                                Belum ada departemen yang sesuai dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($departments->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $departments->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Departemen -->
    <x-modal name="add-department-modal" title="Tambah Departemen Baru" maxWidth="lg">
        <form action="{{ route('hr.departments.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kode Departemen (Maks 10 Karakter)</label>
                <input type="text" name="code" required maxlength="10" placeholder="e.g. FIN, ENG, HRD"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 uppercase font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nama Departemen</label>
                <input type="text" name="name" required placeholder="e.g. Finance & Accounting"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Departemen</label>
                <textarea name="description" rows="3" placeholder="Uraian ruang lingkup tugas unit..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
            </div>

            <div class="pt-3 flex justify-end gap-2.5">
                <button type="button" @click="$dispatch('close-modal', 'add-department-modal')" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm transition">
                    Simpan Departemen
                </button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
