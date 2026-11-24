@extends('layouts.guest')

@section('title', 'Masuk ke Sistem')

@section('content')
<div class="min-h-screen flex flex-col lg:flex-row bg-slate-50">
    <!-- Left Hero Section (Desktop-First Branding) -->
    <div class="lg:w-1/2 bg-gradient-to-br from-indigo-900 via-indigo-800 to-slate-900 text-white p-8 lg:p-14 flex flex-col justify-between relative overflow-hidden">
        <!-- Abstract Background Geometric Pattern -->
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                    <radialGradient id="grad1" cx="50%" cy="50%" r="50%">
                        <stop offset="0%" stop-color="#ffffff" />
                        <stop offset="100%" stop-color="#4f46e5" stop-opacity="0" />
                    </radialGradient>
                </defs>
                <circle cx="20" cy="20" r="40" fill="url(#grad1)" />
                <circle cx="80" cy="80" r="40" fill="url(#grad1)" />
            </svg>
        </div>

        <div class="relative z-10">
            <!-- Brand Logo -->
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-white/10 backdrop-blur border border-white/20 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg">
                    <svg class="w-6 h-6 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <span class="text-2xl font-extrabold tracking-tight text-white">Nas<span class="text-indigo-400">HRIS</span></span>
                    <span class="block text-xs text-indigo-200/80 font-medium tracking-wide">Enterprise HR Management</span>
                </div>
            </div>

            <!-- Value Proposition -->
            <div class="mt-12 lg:mt-20">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-400/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Versi 1.3 - Ready to Production
                </span>
                <h1 class="mt-4 text-3xl lg:text-4xl font-extrabold tracking-tight text-white leading-tight">
                    Sistem Tata Kelola SDM Terintegrasi & Andal
                </h1>
                <p class="mt-3 text-base text-indigo-200/90 leading-relaxed max-w-lg">
                    Otomatisasi siklus data kepegawaian, pemantauan presensi shift, persetujuan cuti berjenjang, dan kalkulasi penggajian transaksional yang aman dan akurat.
                </p>

                <!-- Feature Highlights -->
                <div class="mt-8 space-y-3.5 max-w-lg">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-6 h-6 rounded-lg bg-indigo-500/30 flex items-center justify-center text-indigo-300 mt-0.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        <p class="text-sm text-indigo-100"><strong>Master Organisasi & Karyawan:</strong> Manajemen Departemen, Jabatan, Dokumen Digital & NIK Otomatis.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-6 h-6 rounded-lg bg-indigo-500/30 flex items-center justify-center text-indigo-300 mt-0.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        <p class="text-sm text-indigo-100"><strong>Presensi & Shift Kerja:</strong> Clock-In/Out akurat dengan kalkulasi otomatis toleransi keterlambatan.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-6 h-6 rounded-lg bg-indigo-500/30 flex items-center justify-center text-indigo-300 mt-0.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        <p class="text-sm text-indigo-100"><strong>Alur Cuti 2 Tahap:</strong> Verifikasi bertingkat (Manager & HR) dengan pemotongan kuota otomatis.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-6 h-6 rounded-lg bg-indigo-500/30 flex items-center justify-center text-indigo-300 mt-0.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        <p class="text-sm text-indigo-100"><strong>Engine Penggajian:</strong> Generate payroll bulanan berintegritas transaksi & cetak Slip PDF resmi.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative z-10 mt-8 pt-6 border-t border-indigo-700/50 text-xs text-indigo-300/70 flex justify-between items-center">
            <span>&copy; {{ date('Y') }} Muhammad Nashir Lidienillah. All rights reserved.</span>
            <span>Enterprise Edition</span>
        </div>
    </div>

    <!-- Right Login Section -->
    <div class="lg:w-1/2 flex items-center justify-center p-6 sm:p-10 lg:p-14">
        <div class="w-full max-w-md">
            <!-- Header Text -->
            <div class="mb-8">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Masuk ke Akun Anda</h2>
                <p class="mt-2 text-sm text-slate-600">Gunakan akun kepegawaian Anda atau coba melalui tombol demo di bawah.</p>
            </div>

            <!-- Flash Notifications -->
            @if(session('success'))
                <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-800 flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-5 p-4 rounded-xl bg-sky-50 border border-sky-200 text-sm text-sky-800 flex items-center gap-3">
                    <svg class="w-5 h-5 text-sky-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-sm text-rose-800">
                    <div class="flex items-center gap-2 font-semibold">
                        <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <span>Gagal Masuk</span>
                    </div>
                    <ul class="mt-2 list-disc list-inside text-xs space-y-1 text-rose-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Standard Login Form -->
            <form action="{{ route('login.attempt') }}" method="POST" class="space-y-4" x-data="{ showPassword: false }">
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Email Perusahaan</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>
                        </div>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                            placeholder="nama@nashris.test"
                            class="block w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm bg-white" />
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Kata Sandi</label>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </div>
                        <input id="password" name="password" :type="showPassword ? 'text' : 'password'" required
                            placeholder="••••••••"
                            class="block w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-sm bg-white" />
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" value="1" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 transition">
                        <span class="text-xs font-medium text-slate-600">Ingat Saya</span>
                    </label>
                    <span class="text-xs text-slate-400">Default: <code class="font-mono text-indigo-600 bg-indigo-50 px-1 py-0.5 rounded">password123</code></span>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3 min-h-[44px] px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold text-sm shadow-md shadow-indigo-600/20 hover:shadow-lg hover:shadow-indigo-600/30 transition duration-150 flex items-center justify-center gap-2">
                    <span>Masuk ke NasHRIS</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
