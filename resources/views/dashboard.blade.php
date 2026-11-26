@extends('layouts.guest')

@section('title', 'Dashboard')

@section('content')
<div class="min-h-screen bg-slate-50">
    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-6 lg:px-10 sticky top-0 z-30 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md shadow-indigo-600/30">
                N
            </div>
            <div>
                <span class="text-xl font-extrabold tracking-tight text-slate-900">Nas<span class="text-indigo-600">HRIS</span></span>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <!-- User Status Badge -->
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center border border-indigo-200">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div class="hidden sm:block text-left">
                    <div class="text-xs font-bold text-slate-800">{{ $user->name }}</div>
                    <div class="text-[10px] font-semibold text-indigo-600 uppercase tracking-wider">
                        Role: {{ $user->roles->pluck('name')->join(', ') ?: 'N/A' }}
                    </div>
                </div>
            </div>

            <!-- Logout Form -->
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-300 hover:border-rose-300 hover:bg-rose-50 text-xs font-semibold text-slate-700 hover:text-rose-700 transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="max-w-7xl mx-auto px-6 py-8">
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-800 flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Welcome Banner -->
        <div class="p-6 lg:p-8 rounded-2xl bg-gradient-to-r from-indigo-700 via-indigo-600 to-indigo-800 text-white shadow-lg relative overflow-hidden">
            <div class="relative z-10">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-indigo-100 backdrop-blur">
                    Phase 2 Active & Verified
                </span>
                <h1 class="mt-3 text-2xl lg:text-3xl font-extrabold">Selamat Datang di NasHRIS, {{ $user->name }}!</h1>
                <p class="mt-2 text-indigo-100 text-sm max-w-2xl">
                    Anda berhasil masuk menggunakan akun dengan hak akses <strong class="text-white underline">{{ strtoupper($user->roles->pluck('name')->join(', ')) }}</strong>.
                    @if($user->employee)
                        Departemen: <strong>{{ $user->employee->department->name ?? '-' }}</strong> &bull; Jabatan: <strong>{{ $user->employee->position->title ?? '-' }}</strong> &bull; NIK: <code class="bg-indigo-900/50 px-1.5 py-0.5 rounded">{{ $user->employee->employee_code }}</code>
                    @endif
                </p>
            </div>
        </div>

        <!-- Role Access Information Grid -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-200 flex items-center justify-center text-indigo-600 font-bold mb-4">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Peran & Otorisasi</h3>
                <p class="mt-1 text-xs text-slate-600 leading-relaxed">
                    Sistem menggunakan Spatie Permission dengan middleware <code>role:hr|manager|employee</code> dan Anti-IDOR Policies.
                </p>
                <div class="mt-4 pt-3 border-t border-slate-100 text-xs font-semibold text-indigo-600">
                    Role Aktif: {{ strtoupper($user->roles->pluck('name')->join(', ')) }}
                </div>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 font-bold mb-4">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" /></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Data Master & Karyawan</h3>
                <p class="mt-1 text-xs text-slate-600 leading-relaxed">
                    Database Seeder telah menginisialisasi 4 Departemen, 10 Jabatan, 2 Shift, 1 HR Admin, 4 Manager, dan 30 Karyawan.
                </p>
                <div class="mt-4 pt-3 border-t border-slate-100 text-xs font-semibold text-amber-700">
                    Status: Master Seeder Siap
                </div>
            </div>

            <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 font-bold mb-4">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" /></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Langkah Berikutnya (Phase 3)</h3>
                <p class="mt-1 text-xs text-slate-600 leading-relaxed">
                    Phase 3 akan mengimplementasikan layout navigasi sidebar <code>&lt;x-app-layout&gt;</code> dan CRUD Direktori Karyawan.
                </p>
                <div class="mt-4 pt-3 border-t border-slate-100 text-xs font-semibold text-emerald-700">
                    Roadmap: Phase 3 Ready
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
