@extends('layouts.guest')

@section('title', 'Wajib Ganti Kata Sandi')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50 px-4 py-12">
    <div class="max-w-md w-full bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
        <!-- Brand Header -->
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold shadow-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <div>
                <h1 class="text-lg font-extrabold text-slate-900">Ganti Kata Sandi Sementara</h1>
                <p class="text-xs text-slate-500">Halo, <span class="font-semibold text-slate-700">{{ auth()->user()->name }}</span></p>
            </div>
        </div>

        @if(session('warning'))
            <div class="mb-5 p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 font-medium">
                {{ session('warning') }}
            </div>
        @else
            <div class="mb-5 p-3.5 rounded-xl bg-indigo-50 border border-indigo-200 text-xs text-indigo-800 font-medium">
                Ini adalah login pertama Anda dengan kata sandi sementara. Demi keamanan, buat kata sandi baru minimal 8 karakter sebelum melanjutkan.
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-700 space-y-1">
                @foreach($errors->all() as $error)
                    <div>&bull; {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('password.change.update') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi Baru</label>
                <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password"
                       placeholder="Minimal 8 karakter"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Konfirmasi Kata Sandi Baru</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" autocomplete="new-password"
                       placeholder="Ulangi kata sandi baru"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
            </div>

            <button type="submit"
                    class="w-full py-2.5 min-h-[44px] flex items-center justify-center px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm shadow-sm transition">
                Simpan Kata Sandi &amp; Lanjutkan
            </button>
        </form>

        <div class="mt-5 pt-4 border-t border-slate-100 text-center">
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-xs font-semibold text-slate-500 hover:text-rose-600 transition">
                    Keluar dari Akun
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
