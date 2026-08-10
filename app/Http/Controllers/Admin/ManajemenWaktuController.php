<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManajemenWaktu;
use Illuminate\Http\Request;

class ManajemenWaktuController extends Controller
{
    public function index()
    {
        $waktu = ManajemenWaktu::orderBy('satuan')->orderBy('interval')->get();
        return view('admin.waktu.index', compact('waktu'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'     => 'required|string|max:100|unique:manajemen_waktu,nama',
            'interval' => 'required|integer|min:1',
            'satuan'   => 'required|in:hari,minggu,bulan,tahun',
        ]);

        // Huruf kecil semua untuk nama sebagai "kode/ID" di form jadwal
        $validated['nama'] = strtolower(str_replace(' ', '_', $validated['nama']));

        ManajemenWaktu::create($validated);

        return redirect()->route('admin.waktu.index')->with('success', 'Master Waktu berhasil ditambahkan.');
    }

    public function update(Request $request, ManajemenWaktu $waktu)
    {
        $validated = $request->validate([
            'nama'     => 'required|string|max:100|unique:manajemen_waktu,nama,' . $waktu->id,
            'interval' => 'required|integer|min:1',
            'satuan'   => 'required|in:hari,minggu,bulan,tahun',
        ]);

        $validated['nama'] = strtolower(str_replace(' ', '_', $validated['nama']));
        $waktu->update($validated);

        return redirect()->route('admin.waktu.index')->with('success', 'Master Waktu berhasil diperbarui.');
    }

    public function destroy(ManajemenWaktu $waktu)
    {
        $waktu->delete();
        return redirect()->route('admin.waktu.index')->with('success', 'Master Waktu berhasil dihapus.');
    }
}