<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shared\UpdatePasswordRequest;
use App\Http\Requests\Shared\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();
        $employee = $user->employee?->load(['department', 'position', 'shift']);

        return view('shared.profile.edit', compact('user', 'employee'));
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $employee = $request->user()->employee;
        $employee->update($request->validated());

        return back()->with('success', 'Informasi kontak dan alamat profil berhasil diperbarui.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => Hash::make($request->validated('password')),
            'must_change_password' => false,
        ]);

        return back()->with('success', 'Kata sandi akun berhasil diperbarui.');
    }
}
