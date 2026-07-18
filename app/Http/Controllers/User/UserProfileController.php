<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserProfileController extends Controller
{
    /**
     * Menampilkan halaman profil user.
     */
    public function edit(Request $request): View
    {
        return view('user.profil_user', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Menampilkan halaman edit profil.
     */
    public function editProfile(Request $request): View
    {
        return view('user.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Memperbarui data profil.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user->name = $request->name;
        $user->save();

        return redirect()
            ->route('user.profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Menghapus akun.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        auth()->logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}