<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Pengecekan;

class AkunController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ringkasan tugas harian/total
        $totalSelesai = Pengecekan::where('user_id', $user->id)->count();
        $totalAman    = Pengecekan::where('user_id', $user->id)->where('status', 'aman')->count();
        $totalRusak   = Pengecekan::where('user_id', $user->id)->where('status', 'perlu_tindakan')->count();

        $hariIni = Pengecekan::where('user_id', $user->id)->whereDate('checked_at', today())->count();

        return view('user.akun.index', compact('user', 'totalSelesai', 'totalAman', 'totalRusak', 'hariIni'));
    }

    public function updateProfil(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
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

        return redirect()->route('user.akun.index')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'password_lama' => 'required',
            'password'      => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($validated['password_lama'], $user->password)) {
            return redirect()->route('user.akun.index')->with('error', 'Password lama salah.');
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        return redirect()->route('user.akun.index')->with('success', 'Password berhasil diperbarui.');
    }
}