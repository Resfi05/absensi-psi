<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Carbon\Carbon; // Tambahkan Carbon untuk memanipulasi waktu

class ScanController extends Controller
{
    public function index($kode_barang)
    {
        // Cari barang berdasarkan kode unik dari QR Code
        $barang = Barang::with('kategori', 'lokasiRelasi')->where('kode_barang', $kode_barang)->firstOrFail();

        return view('scan.index', compact('barang'));
    }

    // Tambahkan Request $request di sini untuk menangkap filter dari URL
    public function auditorStory(Request $request, $kode_barang)
    {
        $barang = Barang::with('kategori', 'lokasiRelasi')->where('kode_barang', $kode_barang)->firstOrFail();
        
        // Tangkap filter dari URL, default-nya 'semua'
        $filter = $request->query('filter', 'semua');

        // Siapkan Query Dasar Riwayat Pengecekan
        $query = \App\Models\Pengecekan::whereHas('jadwal', function($q) use ($barang) {
            $q->where('barang_id', $barang->id);
        })->with('user')->orderBy('checked_at', 'desc');

        // Jalankan Filter Waktu Secara Dinamis
        if ($filter === 'minggu_ini') {
            $query->whereBetween('checked_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($filter === 'bulan_ini') {
            $query->whereMonth('checked_at', Carbon::now()->month)
                  ->whereYear('checked_at', Carbon::now()->year);
        } elseif ($filter === 'tahun_ini') {
            $query->whereYear('checked_at', Carbon::now()->year);
        }

        // Eksekusi Query untuk mengambil data yang sudah difilter
        $riwayat = $query->get();

        // Hitung statistik untuk grafik berdasarkan data yang TAMPIL saja
        $totalPengecekan = $riwayat->count();
        $totalAman = $riwayat->where('status', 'aman')->count();
        $totalPerluTindakan = $riwayat->where('status', 'perlu_tindakan')->count();

        // Jangan lupa sertakan variabel $filter agar tombol di tampilan bisa mendeteksi status aktif
        return view('scan.auditor', compact(
            'barang', 
            'riwayat', 
            'totalPengecekan', 
            'totalAman', 
            'totalPerluTindakan', 
            'filter'
        ));
    }
}