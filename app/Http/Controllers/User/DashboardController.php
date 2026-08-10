<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Pengecekan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ── Logika Pencarian Jadwal ──
        $jadwalQuery = Jadwal::with(['barang.kategori', 'barang.lokasiRelasi'])
            ->where(function ($q) use ($user) {
                // Jika sistem mengandalkan Spesialisasi
                if ($user->spesialisasi_id) {
                    $q->orWhereHas('barang', fn($b) => $b->where('kategori_id', $user->spesialisasi_id));
                }
            });

        $bulan = now()->format('Y-m');
        [$tahun, $bln] = explode('-', $bulan);

        // Sembunyikan 'jenis_jadwal' = 'perbaikan' agar tidak double-count di grafik total
        $jadwalBulanIni = (clone $jadwalQuery)
            ->where('jenis_jadwal', '!=', 'perbaikan') 
            ->whereYear('tanggal_jadwal', $tahun)
            ->whereMonth('tanggal_jadwal', $bln)
            ->get();

        $totalTugas     = $jadwalBulanIni->count();
        $selesaiTugas   = 0;
        $perluTindak    = 0;
        $terlambatTugas = 0;

        foreach ($jadwalBulanIni as $j) {
            if ($j->status === 'selesai') {
                $p = $j->pengecekan->sortByDesc('created_at')->first();
                if ($p && $p->status === 'perlu_tindakan' && !in_array($p->status_tindak_lanjut, ['selesai', 'diabaikan'])) {
                    $perluTindak++;
                } else {
                    $selesaiTugas++;
                }
            } else {
                if ($j->isTerlambat()) {
                    $terlambatTugas++;
                }
            }
        }

        $persenSelesai = $totalTugas > 0 ? round($selesaiTugas / $totalTugas * 100, 1) : 0;
        $persenPerlu   = $totalTugas > 0 ? round($perluTindak / $totalTugas * 100, 1) : 0;
        $persenLambat  = $totalTugas > 0 ? round($terlambatTugas / $totalTugas * 100, 1) : 0;

        // ── REVISI: FITUR MULTI-TUGAS BISA DI-SWIPE DENGAN PRIORITAS URUTAN ──
        // 1. Cari tanggal terdekat yang punya tugas belum dikerjakan
        $tanggalTerdekat = (clone $jadwalQuery)
            ->whereIn('status', ['pending', 'belum', 'belum_dikerjakan'])
            ->min('tanggal_jadwal');

        $daftarTugasSelanjutnya = collect();

        if ($tanggalTerdekat) {
            // 2. Ambil SEMUA tugas pada tanggal terdekat tersebut
            $tugasMentah = (clone $jadwalQuery)
                ->whereIn('status', ['pending', 'belum', 'belum_dikerjakan'])
                ->whereDate('tanggal_jadwal', $tanggalTerdekat)
                ->get();

            // 3. Urutkan dengan logika spesifik
            $daftarTugasSelanjutnya = $tugasMentah->sort(function ($a, $b) use ($user) {
                // Penentuan Skor A
                $scoreA = 3; // Default: Bukan spesialisasi
                if ($a->jenis_jadwal === 'perbaikan') {
                    $scoreA = 1; // Prioritas Tertinggi: Tindak Lanjut
                } elseif ($a->barang && $a->barang->kategori_id == $user->spesialisasi_id) {
                    $scoreA = 2; // Prioritas Kedua: Spesialisasi cocok
                }

                // Penentuan Skor B
                $scoreB = 3;
                if ($b->jenis_jadwal === 'perbaikan') {
                    $scoreB = 1;
                } elseif ($b->barang && $b->barang->kategori_id == $user->spesialisasi_id) {
                    $scoreB = 2;
                }

                // Bandingkan skor
                return $scoreA <=> $scoreB;
            })->values(); // Reset index array
        }

        // ── Notifikasi badge (untuk topbar) ──
        $notifBadge = (clone $jadwalQuery)
            ->whereIn('status', ['pending', 'belum', 'belum_dikerjakan'])
            ->whereDate('tanggal_jadwal', '<=', today())
            ->count();

        return view('user.dashboard', compact(
            'user', 'totalTugas', 'selesaiTugas', 'perluTindak', 'terlambatTugas',
            'persenSelesai', 'persenPerlu', 'persenLambat',
            'daftarTugasSelanjutnya', 'notifBadge', 'bulan' 
        ));
    }
}