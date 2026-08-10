<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\Lokasi;
use App\Models\RiwayatBarang;
use App\Exports\BarangExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('export') && $request->export === 'excel') {
            $export = new BarangExport($request->only(['kategori', 'lokasi', 'status', 'search']));
            return $export->download();
        }

        $query = Barang::with(['kategori', 'lokasiRelasi', 'qrCode'])->withCount('jadwal');

        if ($request->filled('kategori') && $request->kategori !== 'semua') {
            $query->where('kategori_id', $request->kategori);
        }

        if ($request->filled('lokasi')) {
            $query->where('lokasi_id', $request->lokasi);
        }

        if ($request->filled('status')) {
            if ($request->status === 'aktif') {
                $query->where('is_active', true);
            } elseif ($request->status === 'nonaktif') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_barang', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%")
                  ->orWhereHas('lokasiRelasi', fn($q2) => $q2->where('nama', 'like', "%{$search}%"));
            });
        }

        $perPage = $request->get('per_page', 10);
        $query->orderBy('kode_barang');

        $barang        = $query->paginate($perPage)->withQueryString();
        $lokasi_list   = Lokasi::where('is_active', true)->orderBy('nama')->get();
        $kategori_list = KategoriBarang::where('is_active', true)->orderBy('nama')->get();
        $total_all     = Barang::count();

        return view('admin.barang.index', compact(
            'barang', 'lokasi_list', 'kategori_list', 'total_all'
        ));
    }

    public function create()
    {
        $kategori_list = KategoriBarang::where('is_active', true)->orderBy('nama')->get();
        $lokasi_list   = Lokasi::where('is_active', true)->orderBy('nama')->get();
        return view('admin.barang.create', compact('kategori_list', 'lokasi_list'));
    }

    public function store(Request $request)
    {
        $kategori = KategoriBarang::find($request->kategori_id);
        $isApar = $kategori && stripos($kategori->nama, 'APAR') !== false;

        $validated = $request->validate([
            'kode_barang'     => 'required|string|max:50|unique:barang,kode_barang',
            'nama_barang'     => 'required|string|max:255',
            'kategori_id'     => 'required|exists:kategori_barang,id',
            'lokasi_id'       => 'required|exists:lokasi,id',
            'tanggal_expired' => $isApar ? 'required|date' : 'nullable|date',
            'merk'            => 'nullable|string|max:150', // Kolom baru
            'model'           => 'nullable|string|max:150', // Kolom baru
            'tipe'            => 'nullable|string|max:150', // Kolom baru
            'keterangan'      => 'nullable|string',
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'tanggal_expired.required' => 'Tanggal Kedaluwarsa (Expired) WAJIB DIISI untuk kategori barang ini.'
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('barang', 'public');
        }

        $lokasi = Lokasi::find($validated['lokasi_id']);
        $validated['jenis_barang'] = $kategori->nama;
        $validated['lokasi']       = $lokasi->nama;
        $validated['is_active']    = $request->has('is_active');

        Barang::create($validated);

        return redirect()
            ->route('admin.barang.index')
            ->with('success', "Barang {$validated['kode_barang']} berhasil ditambahkan.");
    }

    public function show(Barang $barang)
    {
        $barang->load(['qrCode', 'kategori', 'lokasiRelasi', 'pengecekan' => function ($q) {
            $q->with('user')->latest()->take(10);
        }]);

        $riwayatBarang = RiwayatBarang::where('barang_id', $barang->id)
            ->latest()
            ->get()
            ->map(function($r) {
                $r->nama_lokasi_lama = Lokasi::find($r->lokasi_lama_id)->nama ?? 'Tidak Diketahui';
                $r->nama_lokasi_baru = Lokasi::find($r->lokasi_baru_id)->nama ?? 'Tidak Diketahui';
                return $r;
            });

        return view('admin.barang.show', compact('barang', 'riwayatBarang'));
    }

    public function edit(Barang $barang)
    {
        $kategori_list = KategoriBarang::where('is_active', true)->orderBy('nama')->get();
        $lokasi_list   = Lokasi::where('is_active', true)->orderBy('nama')->get();
        return view('admin.barang.edit', compact('barang', 'kategori_list', 'lokasi_list'));
    }

    public function update(Request $request, Barang $barang)
    {
        $kategori = KategoriBarang::find($request->kategori_id);
        $isApar = $kategori && stripos($kategori->nama, 'APAR') !== false;

        $validated = $request->validate([
            'kode_barang'     => ['required', 'string', 'max:50', Rule::unique('barang', 'kode_barang')->ignore($barang->id)],
            'nama_barang'     => 'required|string|max:255',
            'kategori_id'     => 'required|exists:kategori_barang,id',
            'lokasi_id'       => 'required|exists:lokasi,id',
            'tanggal_expired' => $isApar ? 'required|date' : 'nullable|date',
            'merk'            => 'nullable|string|max:150', // Kolom baru
            'model'           => 'nullable|string|max:150', // Kolom baru
            'tipe'            => 'nullable|string|max:150', // Kolom baru
            'keterangan'      => 'nullable|string',
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'tanggal_expired.required' => 'Tanggal Kedaluwarsa (Expired) WAJIB DIISI untuk kategori barang ini.'
        ]);

        if ($request->hasFile('foto')) {
            if ($barang->foto) Storage::disk('public')->delete($barang->foto);
            $validated['foto'] = $request->file('foto')->store('barang', 'public');
        }

        $lokasi = Lokasi::find($validated['lokasi_id']);
        $validated['jenis_barang'] = $kategori->nama;
        $validated['lokasi']       = $lokasi->nama;
        $validated['is_active']    = $request->has('is_active');

        $lokasiLamaId = $barang->lokasi_id;
        $statusLama = $barang->is_active;

        $barang->update($validated);

        if ($lokasiLamaId != $barang->lokasi_id || $statusLama != $barang->is_active) {
            RiwayatBarang::create([
                'barang_id'      => $barang->id,
                'lokasi_lama_id' => $lokasiLamaId,
                'lokasi_baru_id' => $barang->lokasi_id,
                'status_lama'    => $statusLama,
                'status_baru'    => $barang->is_active,
                'keterangan'     => 'Pembaruan data dari halaman Admin',
            ]);
        }

        return redirect()
            ->route('admin.barang.index')
            ->with('success', "Barang {$barang->kode_barang} berhasil diperbarui.");
    }

    public function destroy(Barang $barang)
    {
        if ($barang->foto) Storage::disk('public')->delete($barang->foto);
        $kode = $barang->kode_barang;
        $barang->delete();

        return redirect()
            ->route('admin.barang.index')
            ->with('success', "Barang {$kode} berhasil dihapus.");
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:barang,id',
        ]);

        $barangToDelete = Barang::whereIn('id', $request->ids)
            ->doesntHave('jadwal')
            ->get();

        $count = 0;
        foreach ($barangToDelete as $b) {
            if ($b->foto) {
                Storage::disk('public')->delete($b->foto);
            }
            $b->delete();
            $count++;
        }

        $skipped = count($request->ids) - $count;
        $msg = "{$count} data barang berhasil dihapus.";
        
        if ($skipped > 0) {
            $msg .= " ({$skipped} barang dilewati karena sedang dipakai dalam jadwal/riwayat).";
        }

        return redirect()
            ->route('admin.barang.index')
            ->with('success', $msg);
    }
}