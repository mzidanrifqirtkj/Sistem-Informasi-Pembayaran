<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string', // Validasi untuk input login
            'password' => 'required|string',
        ]);

        // Cek apakah login menggunakan NIS
        $user = User::where('email', $request->login)->first() ??
            Santri::where('nis', $request->login)->first() ??
            Santri::where('no_hp', $request->login)->first();

        if ($user) {
            // Cek password dan login sesuai role
            if (Auth::attempt(['email' => $user->email, 'password' => $request->password])) {
                return redirect()->route('dashboard');
            }
        }

        return back()->withErrors(['login' => 'NIS, No HP atau Email tidak ditemukan atau password salah']);
        // $credentials = $request->validate([
        //     'no_hp' => 'required|string', // Untuk santri
        //     'nis' => 'required|string', // Untuk santri
        //     'email' => 'required|string|email', // Untuk admin
        //     'password' => 'required|string',
        // ]);

        // // Cek login untuk santri menggunakan NIS
        // $santri = Santri::where('nis', $request->nis)->orWhere('no_hp', $request->no_hp)->first();
        // if ($santri && $santri->verifyPassword($request->password)) {
        //     Auth::guard('santri')->login($santri);
        //     return redirect()->route('dashboard.santri');
        // }

        // // Cek login untuk admin menggunakan email
        // if (Auth::guard('web')->attempt($request->only('email', 'password'))) {
        //     return redirect()->route('dashboard.admin');
        // }

        // return back()->withErrors([
        //     'nis' => 'NIS atau password salah',
        //     'no_hp' => 'Nomor HP atau password salah',
        //     'email' => 'email atau password salah'
        // ]);

    }

    public function logout(Request $request)
    {
        Auth::logout();
        // Auth::guard('web')->logout();
        // Auth::guard('santri')->logout();
        return redirect('/');
    }
}
