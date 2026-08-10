<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\Jadwal;
use App\Models\Pengecekan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $bulan = now()->month;
        $tahun = now()->year;

        // Stat Cards per Kategori (dinamis)
        $kategori_stats = KategoriBarang::where('is_active', true)
            ->orderBy('nama')
            ->get()
            ->map(function ($k) {
                $total = Barang::where('kategori_id', $k->id)->count();
                $aktif = Barang::where('kategori_id', $k->id)->where('is_active', true)->count();
                return [
                    'id'       => $k->id,
                    'nama'     => $k->nama,
                    'kode'     => $k->kode,
                    'warna'    => $k->warna,
                    'total'    => $total,
                    'aktif'    => $aktif,
                    'nonaktif' => $total - $aktif,
                ];
            });

        // Total semua barang
        $total_barang = Barang::count();
        $total_aktif  = Barang::where('is_active', true)->count();

        // Jadwal bulan ini
        $total_jadwal = Jadwal::whereMonth('tanggal_jadwal', $bulan)
                            ->whereYear('tanggal_jadwal', $tahun)->count();
        $selesai      = Jadwal::whereMonth('tanggal_jadwal', $bulan)
                            ->whereYear('tanggal_jadwal', $tahun)->where('status', 'selesai')->count();
        $pending      = Jadwal::whereMonth('tanggal_jadwal', $bulan)
                            ->whereYear('tanggal_jadwal', $tahun)->where('status', 'pending')->count();
        $terlambat    = Jadwal::whereMonth('tanggal_jadwal', $bulan)
                            ->whereYear('tanggal_jadwal', $tahun)->where('status', 'terlambat')->count();

        // Perlu Tindak Lanjut
        $perlu_tindak_lanjut = Pengecekan::where('status', 'perlu_tindakan')
            ->whereNotIn('status_tindak_lanjut', ['selesai', 'diabaikan'])
            ->count();

        // 🔥 REVISI 2: Deteksi Barang Mendekati Expired (<= 30 Hari atau Sudah Lewat) 🔥
        $expired_items = Barang::with('kategori')
            ->whereNotNull('tanggal_expired')
            ->whereDate('tanggal_expired', '<=', now()->addDays(30)->toDateString())
            ->orderBy('tanggal_expired', 'asc')
            ->get();
        $expired_count = $expired_items->count();

        // Jadwal Hari Ini
        $jadwal_hari_ini = Jadwal::with(['barang.kategori', 'barang.lokasiRelasi', 'pengecekan'])
            ->whereDate('tanggal_jadwal', today())
            ->orderBy('status')
            ->take(8)
            ->get();

        // Pengecekan Terbaru
        $pengecekan_terbaru = Pengecekan::with(['jadwal.barang.kategori', 'jadwal.barang.lokasiRelasi', 'user'])
            ->latest('checked_at')
            ->take(5)
            ->get();

        // Chart Data
        $start  = Carbon::now()->startOfMonth();
        $end    = Carbon::now()->endOfMonth();
        $labels = [];

        for ($date = $start->copy(); $date->lte($end); $date->addWeek()) {
            $weekEnd  = $date->copy()->addDays(6)->min($end);
            $labels[] = $date->format('d M') . ' — ' . $weekEnd->format('d M');
        }

        $allKategori    = KategoriBarang::where('is_active', true)->orderBy('nama')->get();
        $chart_datasets = [];

        foreach ($allKategori as $k) {
            $data  = [];
            $color = $k->warna ?? '#2563EB';

            for ($date = $start->copy(); $date->lte($end); $date->addWeek()) {
                $weekEnd = $date->copy()->addDays(6)->min($end);

                $data[] = Pengecekan::whereHas('jadwal.barang', fn($q) => $q->where('kategori_id', $k->id))
                    ->whereBetween('checked_at', [
                        $date->copy()->startOfDay(),
                        $weekEnd->copy()->endOfDay(),
                    ])
                    ->count();
            }

            $chart_datasets[] = [
                'label'                => $k->nama,
                'data'                 => $data,
                'borderColor'          => $color,
                'backgroundColor'      => $color . '14',
                'borderWidth'          => 2.5,
                'pointBackgroundColor' => $color,
                'pointRadius'          => 4,
                'tension'              => 0.4,
                'fill'                 => true,
            ];
        }

        $chart_data = [
            'labels'   => $labels,
            'datasets' => $chart_datasets,
        ];

        return view('admin.dashboard', compact(
            'kategori_stats', 'total_barang', 'total_aktif',
            'total_jadwal', 'selesai', 'pending', 'terlambat',
            'perlu_tindak_lanjut', 'expired_items', 'expired_count',
            'jadwal_hari_ini', 'pengecekan_terbaru', 'chart_data'
        ));
    }
}