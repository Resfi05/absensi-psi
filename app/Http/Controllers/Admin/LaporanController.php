<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\KategoriBarang;
use App\Models\Lokasi;
use App\Models\Barang;
use App\Models\Pengecekan;
use App\Exports\LaporanExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    private function getLaporanData(Request $request): array
    {
        $tglMulai = $request->filled('tanggal_mulai')
            ? Carbon::parse($request->tanggal_mulai)->startOfDay()
            : now()->startOfMonth();
            
        $tglAkhir = $request->filled('tanggal_akhir')
            ? Carbon::parse($request->tanggal_akhir)->endOfDay()
            : now()->endOfMonth();

        if ($tglAkhir->lt($tglMulai)) {
            [$tglMulai, $tglAkhir] = [$tglAkhir->copy()->startOfDay(), $tglMulai->copy()->endOfDay()];
        }

        $jadwalQuery = Jadwal::with([
            'barang.kategori', 
            'barang.lokasiRelasi', 
            'pengecekan' => fn($q) => $q->latest()
        ])->whereBetween('tanggal_jadwal', [$tglMulai, $tglAkhir]);

        if ($request->filled('kategori') && $request->kategori !== 'semua') {
            $jadwalQuery->whereHas('barang', fn($q) => $q->where('kategori_id', $request->kategori));
        }

        if ($request->filled('lokasi') && $request->lokasi !== 'semua') {
            $jadwalQuery->whereHas('barang', fn($q) => $q->where('lokasi_id', $request->lokasi));
        }

        $allJadwal = $jadwalQuery->orderBy('tanggal_jadwal', 'asc')->get();

        $mapped = $allJadwal->map(function ($jadwal) {
            $p = $jadwal->pengecekan->first();

            $status = match (true) {
                !$p => 'belum_dicek',
                $p->status === 'aman' => 'aman',
                $p->status === 'perlu_tindakan' => in_array($p->status_tindak_lanjut, ['selesai', 'diabaikan'])
                    ? 'selesai_ditutup'
                    : 'proses_perbaikan',
                default => 'belum_dicek',
            };

            return [
                'jadwal' => $jadwal, 
                'pengecekan' => $p, 
                'status' => $status
            ];
        });

        if ($request->filled('status') && $request->status !== 'semua') {
            $mapped = $mapped->filter(fn($m) => $m['status'] === $request->status);
        }

        if ($request->filled('tindak_lanjut') && $request->tindak_lanjut !== 'semua') {
            $mapped = $mapped->filter(fn($m) => $m['pengecekan'] && $m['pengecekan']->status_tindak_lanjut === $request->tindak_lanjut);
        }

        if ($request->filled('search')) {
            $s = strtolower($request->search);
            $mapped = $mapped->filter(function ($m) use ($s) {
                $namaBarang = strtolower($m['jadwal']->barang->nama_barang ?? '');
                $kodeBarang = strtolower($m['jadwal']->barang->kode_barang ?? '');
                $lokasi     = strtolower($m['jadwal']->barang->lokasiRelasi->nama ?? '');

                return str_contains($namaBarang, $s) || str_contains($kodeBarang, $s) || str_contains($lokasi, $s);
            });
        }

        $data = $mapped->values();

        $statsAll = $allJadwal->map(function ($jadwal) {
            $p = $jadwal->pengecekan->first();
            if (!$p) return 'belum_dicek';
            if ($p->status === 'aman') return 'aman';
            if ($p->status === 'perlu_tindakan') {
                return in_array($p->status_tindak_lanjut, ['selesai', 'diabaikan']) ? 'selesai_ditutup' : 'proses_perbaikan';
            }
            return 'belum_dicek';
        });

        return [
            'data'            => $data,
            'tglMulai'        => $tglMulai,
            'tglAkhir'        => $tglAkhir,
            'totalPengecekan' => $allJadwal->count(),
            'kondisiAman'     => $statsAll->filter(fn($s) => $s === 'aman')->count(),
            'prosesPerbaikan' => $statsAll->filter(fn($s) => $s === 'proses_perbaikan')->count(),
            'selesaiDitutup'  => $statsAll->filter(fn($s) => $s === 'selesai_ditutup')->count(),
        ];
    }

    public function index(Request $request)
    {
        $r = $this->getLaporanData($request);

        $perPage = (int) $request->get('per_page', 10);
        $page    = (int) $request->get('page', 1);
        $total   = $r['data']->count();
        $items   = $r['data']->slice(($page - 1) * $perPage, $perPage)->values();

        $laporan = new \Illuminate\Pagination\LengthAwarePaginator(
            $items, $total, $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $kategori_list = KategoriBarang::where('is_active', true)->orderBy('nama')->get();
        $lokasi_list   = Lokasi::where('is_active', true)->orderBy('nama')->get();

        return view('admin.laporan.index', [
            'laporan'         => $laporan,
            'totalPengecekan' => $r['totalPengecekan'],
            'kondisiAman'     => $r['kondisiAman'],
            'prosesPerbaikan' => $r['prosesPerbaikan'],
            'selesaiDitutup'  => $r['selesaiDitutup'],
            'kategori_list'   => $kategori_list,
            'lokasi_list'     => $lokasi_list,
            'tglMulai'        => $r['tglMulai'],
            'tglAkhir'        => $r['tglAkhir'],
        ]);
    }

    public function exportExcel(Request $request)
    {
        $export = new LaporanExport($request->only([
            'kategori', 'lokasi', 'status', 'tindak_lanjut', 'search', 'tanggal_mulai', 'tanggal_akhir'
        ]));
        return $export->download();
    }

    public function exportPdf(Request $request)
    {
        $r = $this->getLaporanData($request);

        $pdf = Pdf::loadView('admin.laporan.pdf', [
            'data'            => $r['data'],
            'tglMulai'        => $r['tglMulai'],
            'tglAkhir'        => $r['tglAkhir'],
            'totalPengecekan' => $r['totalPengecekan'],
            'kondisiAman'     => $r['kondisiAman'],
            'prosesPerbaikan' => $r['prosesPerbaikan'],
            'selesaiDitutup'  => $r['selesaiDitutup'],
        ])
        ->setPaper('a4', 'landscape')
        ->setOption([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'dpi' => 150
        ]);

        $filename = 'Laporan_Pengecekan_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    public function exportCsv(Request $request)
    {
        $r = $this->getLaporanData($request);
        $data = $r['data'];

        $filename = 'Laporan_Pengecekan_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $statusLabel = [
            'aman'             => 'Kondisi Aman',
            'proses_perbaikan' => 'Proses Perbaikan',
            'selesai_ditutup'  => 'Selesai / Ditutup',
            'belum_dicek'      => 'Belum Dicek',
        ];

        $callback = function () use ($data, $statusLabel) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'No', 'Tanggal', 'Kode Barang', 'Nama Barang', 'Kategori',
                'Lokasi', 'Petugas', 'Status Pengecekan', 'Status Tindak Lanjut', 'Foto Bukti', 'Catatan/Temuan'
            ]);

            foreach ($data as $i => $m) {
                $jadwal = $m['jadwal'];
                $p      = $m['pengecekan'];
                $barang = $jadwal->barang;
                
                // 🔥 REVISI: Perbaikan Status Foto Bukti 🔥
                $adaFoto = 'Tidak Ada';
                if ($p) {
                    if ($p->photo_before && $p->photo_after) $adaFoto = 'Ada (Sebelum & Sesudah)';
                    elseif ($p->photo_before || $p->photo_after) $adaFoto = 'Ada (1 Foto)';
                }

                fputcsv($handle, [
                    $i + 1,
                    $p && $p->checked_at
                        ? Carbon::parse($p->checked_at)->format('d/m/Y H:i')
                        : Carbon::parse($jadwal->tanggal_jadwal)->format('d/m/Y'),
                    $barang->kode_barang ?? '-',
                    $barang->nama_barang ?? '-',
                    $barang->kategori->nama ?? '-',
                    $barang->lokasiRelasi->nama ?? '-',
                    $p->user->name ?? '-',
                    $statusLabel[$m['status']] ?? 'Belum Dicek',
                    $p && $p->status_tindak_lanjut ? ucfirst($p->status_tindak_lanjut) : '-',
                    $adaFoto,
                    $p->notes ?? '-',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function wipeDataByCategory(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori_barang,id',
            'username'    => 'required|string',
            'password'    => 'required|string',
        ]);

        $user = Auth::user();
        $validUsername = ($user->username === $request->username) 
                      || ($user->email === $request->username) 
                      || ($user->name === $request->username);
        
        if (!$validUsername) {
            return redirect()->back()->with('error', 'Otorisasi gagal: Username/Email tidak sesuai dengan akun Anda.');
        }

        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()->with('error', 'Otorisasi gagal: Password yang Anda masukkan salah.');
        }

        try {
            DB::transaction(function () use ($request) {
                $kategori = KategoriBarang::findOrFail($request->kategori_id);
                $barangs = Barang::where('kategori_id', $kategori->id)->get();

                foreach ($barangs as $barang) {
                    $jadwals = Jadwal::where('barang_id', $barang->id)->get();
                    foreach ($jadwals as $jadwal) {
                        Pengecekan::where('jadwal_id', $jadwal->id)->delete();
                        $jadwal->delete();
                    }

                    if (class_exists(\App\Models\QrCode::class)) {
                        \App\Models\QrCode::where('barang_id', $barang->id)->delete();
                    }

                    $barang->delete();
                }

                $kategori->delete();
            });

            return redirect()->back()->with('success', 'Pemusnahan data kategori dan seluruh data terkait berhasil dilakukan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memusnahkan data: ' . $e->getMessage());
        }
    }
}