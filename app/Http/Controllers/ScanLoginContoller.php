<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScanLoginController extends Controller
{
    /**
     * AJAX Login khusus untuk halaman portal scan QR.
     * Hanya menerima akun dengan role 'user' (petugas).
     * Admin tidak bisa login lewat sini.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
        ];

        // Coba login
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Hanya petugas (role = 'user') yang boleh login lewat portal scan
            if ($user->role !== 'user') {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Halaman ini hanya untuk Petugas, bukan Admin.',
                ], 403);
            }

            // Cek apakah akun aktif
            if (!$user->is_active) {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda tidak aktif. Hubungi Admin.',
                ], 403);
            }

            // Regenerate session untuk keamanan
            $request->session()->regenerate();

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil.',
                'user'    => $user->name,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah.',
        ], 401);
    }
}