<x-app-layout title="Profil Saya">
    <x-slot name="breadcrumb">
        <span>NasHRIS</span>
        <span>&rsaquo;</span>
        <span class="text-slate-700">Profil Saya</span>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Profile Header Card -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white font-extrabold text-xl flex items-center justify-center shadow-sm">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">{{ $user->name }}</h2>
                    <p class="text-xs text-slate-500">{{ $user->email }}</p>
                    <div class="flex flex-wrap items-center gap-2 mt-1.5">
                        <span class="px-2 py-0.5 rounded-md bg-indigo-50 border border-indigo-200 text-indigo-700 text-[10px] font-bold uppercase tracking-wider">
                            {{ $user->roles->pluck('name')->join(', ') }}
                        </span>
                        @if($employee)
                            <span class="px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 text-slate-700 text-[10px] font-mono font-bold">
                                {{ $employee->employee_code }}
                            </span>
                            <span class="text-[11px] text-slate-500">
                                {{ $employee->position->title ?? '-' }} &bull; {{ $employee->department->name ?? '-' }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Contact & Address Form -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 border-b border-slate-100 pb-3">
                    Informasi Kontak &amp; Alamat
                </h3>

                @if($employee)
                    <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="phone" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nomor HP / WhatsApp</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                        </div>

                        <div>
                            <label for="address" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Alamat Domisili</label>
                            <textarea id="address" name="address" rows="4" required
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none">{{ old('address', $employee->address) }}</textarea>
                        </div>

                        <button type="submit"
                                class="w-full py-2.5 min-h-[44px] flex items-center justify-center px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs transition">
                            Simpan Perubahan Profil
                        </button>
                    </form>
                @else
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600">
                        Akun ini adalah akun Administrator sistem tanpa profil data kepegawaian. Anda tetap dapat memperbarui kata sandi melalui panel di samping.
                    </div>
                @endif
            </div>

            <!-- Change Password Form -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 border-b border-slate-100 pb-3">
                    Ubah Kata Sandi
                </h3>

                <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi Saat Ini</label>
                        <input type="password" id="current_password" name="current_password" required autocomplete="current-password"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi Baru</label>
                        <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password"
                               placeholder="Minimal 8 karakter"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" autocomplete="new-password"
                               placeholder="Ulangi kata sandi baru"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                    </div>

                    <button type="submit"
                            class="w-full py-2.5 min-h-[44px] flex items-center justify-center px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-xs transition">
                        Perbarui Kata Sandi
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
