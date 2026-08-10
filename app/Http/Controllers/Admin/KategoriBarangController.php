<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriBarang;
use Illuminate\Http\Request;

class KategoriBarangController extends Controller
{
    public function index()
    {
        $kategori = KategoriBarang::withCount('barang')->orderBy('nama')->get();
        return view('admin.kategori.index', compact('kategori'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:100|unique:kategori_barang,nama',
            'kode'      => 'required|string|max:20|unique:kategori_barang,kode',
            'warna'     => 'required|string|max:7',
            'deskripsi' => 'nullable|string',
            'checklist' => 'nullable|string', // REVISI 4: Validasi input checklist
        ]);

        $validated['kode']      = strtoupper($validated['kode']);
        $validated['is_active'] = true;
        
        // REVISI: Pastikan checklist ikut tersimpan, jika kosong jadikan null
        $validated['checklist'] = $request->filled('checklist') ? $request->checklist : null;

        KategoriBarang::create($validated);

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', "Kategori {$validated['nama']} berhasil ditambahkan.");
    }

    public function update(Request $request, KategoriBarang $kategori)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:100|unique:kategori_barang,nama,' . $kategori->id,
            'kode'      => 'required|string|max:20|unique:kategori_barang,kode,' . $kategori->id,
            'warna'     => 'required|string|max:7',
            'deskripsi' => 'nullable|string',
            'checklist' => 'nullable|string', // REVISI 4: Validasi edit checklist
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['kode']      = strtoupper($validated['kode']);
        $validated['is_active'] = $request->boolean('is_active', $kategori->is_active);
        
        // REVISI: Pastikan checklist ikut terupdate, jika kosong jadikan null
        $validated['checklist'] = $request->filled('checklist') ? $request->checklist : null;

        $kategori->update($validated);

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', "Kategori {$kategori->nama} berhasil diperbarui.");
    }

    public function destroy(KategoriBarang $kategori)
    {
        if ($kategori->barang()->count() > 0) {
            return redirect()
                ->route('admin.kategori.index')
                ->with('error', "Kategori {$kategori->nama} tidak bisa dihapus karena masih digunakan oleh {$kategori->barang()->count()} barang.");
        }

        $nama = $kategori->nama;
        $kategori->delete();

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', "Kategori {$nama} berhasil dihapus.");
    }
}