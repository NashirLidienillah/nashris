@extends('layouts.guest')

@section('title', 'Masuk ke Sistem')

@section('content')
<div class="min-h-screen flex flex-col lg:flex-row bg-white">

    {{-- ═══════════════════════════════════════════════════════
         LEFT PANEL — Modern Light Workspace (Desktop Only)
    ═══════════════════════════════════════════════════════ --}}
    <div class="hidden lg:flex lg:w-1/2 bg-slate-50 bg-gradient-to-br from-slate-100 via-indigo-50/60 to-slate-100 border-r border-slate-200/80 flex-col justify-between relative overflow-hidden select-none p-10 xl:p-14">

        {{-- Subtle architectural grid pattern --}}
        <div class="absolute inset-0 opacity-[0.45] pointer-events-none" aria-hidden="true">
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="light-grid" width="32" height="32" patternUnits="userSpaceOnUse">
                        <path d="M 32 0 L 0 0 0 32" fill="none" stroke="#cbd5e1" stroke-width="0.75"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#light-grid)" />
            </svg>
        </div>

        {{-- Top: Brand Logo --}}
        <div class="relative z-10 flex items-center gap-3">
            <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md shadow-indigo-600/25">
                N
            </div>
            <div class="leading-none">
                <span class="text-xl font-extrabold tracking-tight text-slate-900">Nas<span class="text-indigo-600">HRIS</span></span>
                <span class="block text-[11px] text-slate-500 font-medium mt-0.5">Enterprise HR</span>
            </div>
        </div>

        {{-- Middle: Headline & Real Workflow Activity Cards --}}
        <div class="relative z-10 my-auto py-8 max-w-lg">
            <h1 class="text-2xl font-semibold text-slate-900 tracking-tight leading-snug">
                Administrasi kepegawaian, presensi, dan penggajian dalam satu sistem.
            </h1>
            <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                Kelola data karyawan, persetujuan cuti berjenjang, hingga distribusi slip gaji bulanan secara terstruktur.
            </p>

            {{-- Activity / Workflow Preview Stack --}}
            <div class="mt-8 space-y-3">
                {{-- Card 1: Presensi --}}
                <div class="bg-white border border-slate-200/90 rounded-xl shadow-xs p-3.5 flex items-center gap-3 transition-all">
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold text-slate-800">Presensi Masuk Tercatat</p>
                            <span class="text-[10px] font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">Berhasil</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5 truncate">Shift Regular (08:00) • Tepat Waktu</p>
                    </div>
                </div>

                {{-- Card 2: Cuti --}}
                <div class="bg-white border border-slate-200/90 rounded-xl shadow-xs p-3.5 flex items-center gap-3 transition-all">
                    <div class="w-9 h-9 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold text-slate-800">Pengajuan Cuti Tahunan</p>
                            <span class="text-[10px] font-medium text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">Approved</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5 truncate">Disetujui Manager & HR • Saldo diperbarui</p>
                    </div>
                </div>

                {{-- Card 3: Payroll --}}
                <div class="bg-white border border-slate-200/90 rounded-xl shadow-xs p-3.5 flex items-center gap-3 transition-all">
                    <div class="w-9 h-9 rounded-lg bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-600 flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold text-slate-800">Slip Gaji Bulanan</p>
                            <span class="text-[10px] font-medium text-sky-700 bg-sky-50 px-2 py-0.5 rounded-md">Status: Paid</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5 truncate">Siap diunduh (PDF resmi)</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom: Copyright --}}
        <div class="relative z-10 pt-4 border-t border-slate-200/70 text-xs text-slate-500">
            <span>&copy; {{ date('Y') }} Muhammad Nashir Lidienillah</span>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         RIGHT PANEL — Clean Login Form
    ═══════════════════════════════════════════════════════ --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center min-h-screen p-6 sm:p-10 lg:p-14 bg-white">
        <div class="w-full max-w-[400px]">

            {{-- Mobile Logo (< lg) --}}
            <div class="flex lg:hidden items-center gap-2.5 mb-8">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold text-base shadow-sm">
                    N
                </div>
                <div class="leading-none">
                    <span class="text-lg font-extrabold tracking-tight text-slate-900">Nas<span class="text-indigo-600">HRIS</span></span>
                    <span class="block text-[10px] text-slate-400 font-medium mt-0.5">Enterprise HR</span>
                </div>
            </div>

            {{-- Heading --}}
            <div class="mb-7">
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Masuk ke Akun Anda</h2>
                <p class="mt-1.5 text-sm text-slate-500">Silakan masukkan email dan kata sandi yang terdaftar.</p>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-800 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-5 p-3.5 rounded-xl bg-sky-50 border border-sky-200 text-sm text-sky-800 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-sky-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-sm text-rose-800">
                    <div class="flex items-center gap-2 font-semibold mb-1">
                        <svg class="w-4 h-4 text-rose-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Gagal Masuk</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-700 ml-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Login Form --}}
            <form action="{{ route('login.attempt') }}" method="POST" class="space-y-4" x-data="{ showPassword: false }">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-xs font-medium text-slate-700 mb-1.5">Email Perusahaan</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="nama@perusahaan.co.id"
                        class="block w-full h-11 px-3.5 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/15 transition"
                    />
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-xs font-medium text-slate-700 mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <input
                            id="password"
                            name="password"
                            :type="showPassword ? 'text' : 'password'"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••••"
                            class="block w-full h-11 px-3.5 pr-11 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/15 transition"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition focus:outline-none"
                            :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                        >
                            <svg x-show="!showPassword" class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showPassword" x-cloak class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center pt-0.5">
                    <label class="flex items-center gap-2 cursor-pointer select-none group">
                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                            class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-600 focus:ring-2 focus:ring-offset-0 transition cursor-pointer"
                        />
                        <span class="text-xs font-medium text-slate-600 group-hover:text-slate-800 transition">Ingat saya</span>
                    </label>
                </div>

                {{-- Submit Button --}}
                <button
                    type="submit"
                    class="w-full h-11 min-h-[44px] rounded-xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-medium text-sm shadow-xs transition duration-150"
                >
                    Masuk
                </button>
            </form>

            {{-- Footer (Mobile only) --}}
            <p class="lg:hidden mt-8 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} Muhammad Nashir Lidienillah
            </p>
        </div>
    </div>

</div>
@endsection
