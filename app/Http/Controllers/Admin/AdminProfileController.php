<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminProfileController extends Controller
{
    public function index(): View
    {
        return view('admin.profile');
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:25',
            ],
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,gif',
                'max:2048',
            ],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email sudah digunakan pengguna lain.',
            'phone.max' => 'Nomor telepon maksimal 25 karakter.',
            'avatar.image' => 'File avatar harus berupa gambar.',
            'avatar.mimes' => 'Avatar harus berformat JPG, JPEG, PNG, atau GIF.',
            'avatar.max' => 'Ukuran avatar maksimal 2 MB.',
        ]);

        if ($request->hasFile('avatar')) {
            if (
                $user->avatar
                && Storage::disk('public')->exists($user->avatar)
            ) {
                Storage::disk('public')->delete($user->avatar);
            }

            $validated['avatar'] = $request
                ->file('avatar')
                ->store('avatars', 'public');
        }

        $user->update($validated);

        return redirect()
            ->route('admin.profile.index')
            ->with('success', 'Profil administrator berhasil diperbarui.');
    }
}