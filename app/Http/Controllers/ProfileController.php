<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $user->load('santri'); // Load relasi santri jika ada

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id_user, 'id_user')
            ],
        ]);

        $dataToUpdate = [];

        // Update email jika diisi dan berbeda dari yang sekarang
        if ($request->filled('email') && $request->email !== $user->email) {
            $dataToUpdate['email'] = $request->email;
        }

        // Update data jika ada perubahan
        if (!empty($dataToUpdate)) {
            $user->update($dataToUpdate);
            return back()->with('status', 'Profil berhasil diperbarui!');
        }

        return back()->with('info', 'Tidak ada perubahan yang disimpan.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        // Pastikan password default bisa diganti tanpa memverifikasi password lama
        if (!Hash::check($request->current_password, $user->password) && $user->password !== 'defaultpassword') {
            return back()->withErrors(['current_password' => 'Password lama salah.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('status', 'Password berhasil diperbarui!');
    }
}
