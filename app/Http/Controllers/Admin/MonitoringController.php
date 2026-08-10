<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Pengecekan;
use App\Models\KategoriBarang;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        // Export Excel
        if ($request->has('export') && $request->export === 'excel') {
            $export = new \App\Exports\MonitoringExport($request->only([
                'kategori', 'lokasi', 'status', 'search', 'tanggal_mulai', 'tanggal_akhir'
            ]));
            return $export->download();
        }

        // Rentang Tanggal (default: bulan ini)
        $tglMulai = $request->filled('tanggal_mulai')
            ? Carbon::parse($request->tanggal_mulai)->startOfDay()
            : now()->startOfMonth();
        $tglAkhir = $request->filled('tanggal_akhir')
            ? Carbon::parse($request->tanggal_akhir)->endOfDay()
            : now()->endOfMonth();

        // Validasi: tanggal akhir tidak boleh sebelum tanggal mulai
        if ($tglAkhir->lt($tglMulai)) {
            [$tglMulai, $tglAkhir] = [$tglAkhir->copy()->startOfDay(), $tglMulai->copy()->endOfDay()];
        }

        // Validasi: batasi maksimal rentang 90 hari
        $rangeWarning = null;
        $maxDays = 90;
        if ($tglMulai->diffInDays($tglAkhir) > $maxDays) {
            $tglAkhir = $tglMulai->copy()->addDays($maxDays)->endOfDay();
            $rangeWarning = "Rentang tanggal dibatasi maksimal {$maxDays} hari agar grafik tetap terbaca. Silakan pilih rentang yang lebih pendek untuk hasil lebih detail.";
        }

        // Base query: semua JADWAL dalam rentang tanggal
        $jadwalQuery = Jadwal::with(['barang.kategori', 'barang.lokasiRelasi', 'pengecekan.user'])
            ->whereBetween('tanggal_jadwal', [$tglMulai, $tglAkhir]);

        // Filter kategori
        if ($request->filled('kategori') && $request->kategori !== 'semua') {
            $jadwalQuery->whereHas('barang', fn($q) => $q->where('kategori_id', $request->kategori));
        }

        // Filter lokasi
        if ($request->filled('lokasi') && $request->lokasi !== 'semua') {
            $jadwalQuery->whereHas('barang', fn($q) => $q->where('lokasi_id', $request->lokasi));
        }

        $allJadwal = $jadwalQuery->get();

        // Hitung status per jadwal (mapping wajib, tidak tumpang tindih)
        $mapped = $allJadwal->map(function ($jadwal) {
            $pengecekanTerakhir = $jadwal->pengecekan->sortByDesc('created_at')->first();

            if (!$pengecekanTerakhir) {
                $statusReal = 'belum_dicek'; // tidak ada record sama sekali
            } elseif ($pengecekanTerakhir->status === 'aman') {
                $statusReal = 'sudah_dicek';
            } elseif ($pengecekanTerakhir->status === 'perlu_tindakan') {
                $statusReal = 'perlu_tindakan';
            } else { // tertunda
                $statusReal = 'belum_dicek';
            }

            return [
                'jadwal'       => $jadwal,
                'pengecekan'   => $pengecekanTerakhir,
                'status_real'  => $statusReal,
            ];
        });

        // Filter status (setelah mapping, karena status_real adalah hasil olahan)
        if ($request->filled('status') && $request->status !== 'semua') {
            $mapped = $mapped->filter(fn($m) => $m['status_real'] === $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $s = strtolower($request->search);
            $mapped = $mapped->filter(function ($m) use ($s) {
                return str_contains(strtolower($m['jadwal']->barang->nama_barang ?? ''), $s)
                    || str_contains(strtolower($m['jadwal']->barang->kode_barang ?? ''), $s)
                    || str_contains(strtolower($m['jadwal']->barang->lokasiRelasi->nama ?? ''), $s);
            });
        }

        $mapped = $mapped->values();

        // Stats (dihitung dari SEMUA jadwal periode ini, sebelum filter status/search)
        $totalBarang = $allJadwal->count();

        $statsAll = $allJadwal->map(function ($jadwal) {
            $p = $jadwal->pengecekan->sortByDesc('created_at')->first();
            if (!$p) return 'belum_dicek';
            if ($p->status === 'aman') return 'sudah_dicek';
            if ($p->status === 'perlu_tindakan') return 'perlu_tindakan';
            return 'belum_dicek';
        });

        $sudahDicek      = $statsAll->filter(fn($s) => $s === 'sudah_dicek')->count();
        $belumDicek      = $statsAll->filter(fn($s) => $s === 'belum_dicek')->count();
        $perluTindak     = $statsAll->filter(fn($s) => $s === 'perlu_tindakan')->count();

        // Pagination manual untuk collection
        $perPage = (int) $request->get('per_page', 10);
        $page    = (int) $request->get('page', 1);
        $total   = $mapped->count();
        $items   = $mapped->slice(($page - 1) * $perPage, $perPage)->values();

        $pengecekan = new \Illuminate\Pagination\LengthAwarePaginator(
            $items, $total, $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Grafik per hari (Chart.js)
        $labels = [];
        $dataSudahDicek = [];
        $dataBelumDicek = [];

        $cursor = $tglMulai->copy();
        while ($cursor->lte($tglAkhir)) {
            $dateKey = $cursor->format('Y-m-d');
            $labels[] = $cursor->format('d M');

            $jadwalHariIni = $allJadwal->filter(fn($j) => Carbon::parse($j->tanggal_jadwal)->format('Y-m-d') === $dateKey);

            $sudahCount = 0;
            $belumCount = 0;
            foreach ($jadwalHariIni as $j) {
                $p = $j->pengecekan->sortByDesc('created_at')->first();
                if ($p && $p->status === 'aman') {
                    $sudahCount++;
                } else {
                    $belumCount++;
                }
            }

            $dataSudahDicek[] = $sudahCount;
            $dataBelumDicek[] = $belumCount;

            $cursor->addDay();
        }

        $chart_data = [
            'labels' => $labels,
            'sudah'  => $dataSudahDicek,
            'belum'  => $dataBelumDicek,
        ];

        // Pengecekan terbaru (untuk panel kanan)
        $terbaru = Pengecekan::with(['jadwal.barang.kategori', 'jadwal.barang.lokasiRelasi', 'user'])
            ->whereNotNull('checked_at')
            ->latest('checked_at')
            ->take(4)
            ->get();

        $kategori_list = KategoriBarang::where('is_active', true)->orderBy('nama')->get();
        $lokasi_list   = Lokasi::where('is_active', true)->orderBy('nama')->get();

        return view('admin.monitoring.index', compact(
            'pengecekan', 'totalBarang', 'sudahDicek', 'belumDicek', 'perluTindak',
            'chart_data', 'terbaru', 'kategori_list', 'lokasi_list',
            'tglMulai', 'tglAkhir', 'rangeWarning'
        ));
    }

    public function show(Pengecekan $pengecekan)
    {
        $pengecekan->load(['jadwal.barang.kategori', 'jadwal.barang.lokasiRelasi', 'user']);
        return view('admin.monitoring.show', compact('pengecekan'));
    }
}