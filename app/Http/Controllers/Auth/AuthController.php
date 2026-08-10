<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Cukup cek username dan password dulu
        $credentials = [
            'username'  => $request->username,
            'password'  => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            
            // 🔥 SATPAM LOGIN: Cek apakah akun dinonaktifkan
            if (!Auth::user()->is_active) {
                // Keluarkan paksa (logout)
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Kembalikan ke halaman login dengan pesan khusus
                return back()->withErrors([
                    'username' => 'Akun Anda telah dinonaktifkan oleh Admin. Silakan hubungi atasan Anda.',
                ])->onlyInput('username');
            }

            // Jika aktif, lolos masuk
            $request->session()->regenerate();
            return $this->redirectByRole();
        }

        // Jika username / password memang salah dari awal
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }

    private function redirectByRole()
    {
        return Auth::user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    }
}