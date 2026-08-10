<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $baseQuery = Jadwal::with(['barang.kategori', 'barang.lokasiRelasi', 'pengecekan'])
            ->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere(function($sub) use ($user) {
                      $sub->whereNull('user_id')
                          ->whereHas('barang', fn($b) => $b->where('kategori_id', $user->spesialisasi_id));
                  });
            })
            ->where('status', '!=', 'selesai'); 

        $tab = $request->get('tab', 'semua');
        $query = clone $baseQuery;

        // 🔥 REVISI: Ubah Filter Besok menjadi Tahun Ini
        if ($tab === 'hari_ini') {
            $query->whereDate('tanggal_jadwal', today());
        } elseif ($tab === 'minggu_ini') {
            $query->whereBetween('tanggal_jadwal', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($tab === 'tahun_ini') {
            $query->whereYear('tanggal_jadwal', now()->year);
        }

        // Sorting
        $sort = $request->get('sort', 'tanggal');
        if ($sort === 'tanggal') {
            $query->orderBy('tanggal_jadwal')->orderBy('jam_mulai');
        } elseif ($sort === 'lokasi') {
            $query->join('barang', 'jadwal.barang_id', '=', 'barang.id')
                  ->orderBy('barang.lokasi_id')
                  ->select('jadwal.*');
        }

        $jadwalList = $query->get();

        $jadwalAktifCount = (clone $baseQuery)->count();
        $terdekat = (clone $baseQuery)
            ->where('tanggal_jadwal', '>=', today())
            ->orderBy('tanggal_jadwal')
            ->orderBy('jam_mulai')
            ->first();

        // 🔥 REVISI: Hitung badge untuk tab Tahun Ini
        $countSemua     = (clone $baseQuery)->count();
        $countHariIni   = (clone $baseQuery)->whereDate('tanggal_jadwal', today())->count();
        $countMingguIni = (clone $baseQuery)->whereBetween('tanggal_jadwal', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $countTahunIni  = (clone $baseQuery)->whereYear('tanggal_jadwal', now()->year)->count();

        return view('user.jadwal.index', compact(
            'jadwalList', 'tab', 'sort',
            'jadwalAktifCount', 'terdekat',
            'countSemua', 'countHariIni', 'countMingguIni', 'countTahunIni'
        ));
    }

    public function show(Jadwal $jadwal)
    {
        $user = Auth::user();

        if ($jadwal->user_id !== $user->id && $jadwal->barang->kategori_id !== $user->spesialisasi_id) {
            abort(403, 'Tugas ini bukan untuk Anda.');
        }

        // 🔥 REVISI: Cegah error jika user memaksa buka lewat URL
        if (Carbon::parse($jadwal->tanggal_jadwal)->startOfDay()->isFuture()) {
            return view('user.jadwal.show', [
                'jadwal' => $jadwal,
                'belumWaktu' => true,
                'pesan' => 'Jadwal ini belum bisa dikerjakan karena baru akan dimulai pada ' . Carbon::parse($jadwal->tanggal_jadwal)->translatedFormat('d M Y') . '.'
            ]);
        }

        $jadwal->load(['barang.kategori', 'barang.lokasiRelasi', 'pengecekan.user']);
        return view('user.jadwal.show', compact('jadwal'));
    }
}