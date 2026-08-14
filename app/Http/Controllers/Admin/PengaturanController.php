<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PengaturanController extends Controller
{
    public function index()
    {
        // 🔥 PERBAIKAN: Tambahkan load('spesialisasi') 
        $user = Auth::user()->load('spesialisasi');
        
        return view('admin.pengaturan.index', compact('user'));
    }

    /**
     * Update profil (nama, email, no hp, foto)
     */
    public function updateProfil(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'no_hp' => 'nullable|string|max:20',
            'foto'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $validated['foto'] = $request->file('foto')->store('users', 'public');
        }

        $user->update($validated);

        return redirect()
            ->route('admin.pengaturan.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'password_lama'         => 'required',
            'password'              => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($validated['password_lama'], $user->password)) {
            return redirect()
                ->route('admin.pengaturan.index')
                ->with('error', 'Password lama yang Anda masukkan salah.');
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('admin.pengaturan.index')
            ->with('success', 'Password berhasil diperbarui.');
    }

    /**
     * Update preferensi notifikasi (eksternal/email saja — internal/badge wajib aktif)
     */
    public function updateNotifikasi(Request $request)
    {
        $user = Auth::user();

        $user->update([
            'email_notification_preference' => $request->boolean('email_notification_preference'),
        ]);

        return redirect()
            ->route('admin.pengaturan.index')
            ->with('success', 'Preferensi notifikasi berhasil diperbarui.');
    }
}