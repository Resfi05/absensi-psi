<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lokasi;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    public function index()
    {
        $lokasi = Lokasi::withCount('barang')->orderBy('nama')->get();
        return view('admin.lokasi.index', compact('lokasi'));
    }

    public function show(Lokasi $lokasi)
    {
        $barang = $lokasi->barang()->with('kategori')->orderBy('kode_barang')->get();

        // Kelompokkan barang per kategori untuk ringkasan
        $per_kategori = $barang->groupBy(fn($b) => $b->kategori?->id ?? 'lainnya')
            ->map(function ($items) {
                $kategori = $items->first()->kategori;
                return [
                    'kategori' => $kategori,
                    'nama'     => $kategori?->nama ?? 'Tanpa Kategori',
                    'warna'    => $kategori?->warna ?? '#94a3b8',
                    'jumlah'   => $items->count(),
                    'aktif'    => $items->where('is_active', true)->count(),
                ];
            })
            ->values();

        $total_aktif = $barang->where('is_active', true)->count();

        return view('admin.lokasi.show', compact('lokasi', 'barang', 'per_kategori', 'total_aktif'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:255|unique:lokasi,nama',
            'kode'      => 'nullable|string|max:20|unique:lokasi,kode',
            'deskripsi' => 'nullable|string',
        ]);

        $validated['is_active'] = true;
        Lokasi::create($validated);

        return redirect()
            ->route('admin.lokasi.index')
            ->with('success', "Lokasi \"{$validated['nama']}\" berhasil ditambahkan.");
    }

    public function update(Request $request, Lokasi $lokasi)
    {
        $validated = $request->validate([
        'nama'      => 'required|string|max:255|unique:lokasi,nama,' . $lokasi->id,
        'kode'      => 'nullable|string|max:20|unique:lokasi,kode,' . $lokasi->id,
        'deskripsi' => 'nullable|string',
    ]);
 
    $validated['is_active'] = $request->has('is_active');
    $lokasi->update($validated);

        return redirect()
            ->route('admin.lokasi.index')
            ->with('success', "Lokasi \"{$lokasi->nama}\" berhasil diperbarui.");
    }

    public function destroy(Lokasi $lokasi)
    {
        if ($lokasi->barang()->count() > 0) {
            return redirect()
                ->route('admin.lokasi.index')
                ->with('error', "Lokasi \"{$lokasi->nama}\" tidak bisa dihapus karena masih digunakan oleh {$lokasi->barang()->count()} barang.");
        }

        $nama = $lokasi->nama;
        $lokasi->delete();

        return redirect()
            ->route('admin.lokasi.index')
            ->with('success', "Lokasi \"{$nama}\" berhasil dihapus.");
    }
}
