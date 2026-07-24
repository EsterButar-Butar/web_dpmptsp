<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    public function index(Request $request): View
    {
        $authLogs = collect();

        return view('admin.settings', compact('authLogs'));
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => [
                'required',
                'current_password',
            ],
            'new_password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak sesuai.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
        ]);

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Password administrator berhasil diperbarui.');
    }

    public function toggleTwoFactor(Request $request): RedirectResponse
    {
        $user = $request->user();

        $user->update([
            'two_factor_enabled' => ! (bool) $user->two_factor_enabled,
        ]);

        $message = $user->two_factor_enabled
            ? 'Autentikasi dua faktor berhasil diaktifkan.'
            : 'Autentikasi dua faktor berhasil dinonaktifkan.';

        return redirect()
            ->route('admin.settings.index')
            ->with('success', $message);
    }
}