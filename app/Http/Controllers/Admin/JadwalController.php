<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Barang;
use App\Models\User;
use App\Models\KategoriBarang;
use App\Models\ManajemenWaktu; 
use App\Models\Notification;
use App\Exports\JadwalExport;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        Jadwal::where('status', 'pending')
              ->whereDate('tanggal_jadwal', '<', now()->format('Y-m-d'))
              ->update(['status' => 'terlambat']);

        if ($request->has('export') && $request->export === 'excel') {
            $export = new JadwalExport($request->only(['kategori', 'lokasi', 'status', 'bulan', 'search']));
            return $export->download();
        }

        $query = Jadwal::with(['barang.kategori', 'barang.lokasiRelasi', 'user']);

        if ($request->filled('kategori') && $request->kategori !== 'semua') {
            $query->whereHas('barang', fn($q) => $q->where('kategori_id', $request->kategori));
        }
        if ($request->filled('lokasi')) {
            $query->whereHas('barang', fn($q) => $q->where('lokasi_id', $request->lokasi));
        }
        
        if ($request->filled('status') && $request->status !== 'semua') {
            if ($request->status === 'jatuh_tempo') {
                $query->where('status', 'pending')
                      ->whereBetween('tanggal_jadwal', [now(), now()->addDays(7)]);
            } else {
                $query->where('status', $request->status);
            }
        }

        $bulan = $request->filled('bulan') ? $request->bulan : now()->format('Y-m');
        [$tahun, $bln] = explode('-', $bulan);
        $query->whereYear('tanggal_jadwal', $tahun)->whereMonth('tanggal_jadwal', $bln);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('judul', 'like', "%{$s}%")
                  ->orWhereHas('barang', fn($q2) => $q2
                      ->where('nama_barang', 'like', "%{$s}%")
                      ->orWhere('kode_barang', 'like', "%{$s}%"))
                  ->orWhereHas('barang.lokasiRelasi', fn($q3) => $q3->where('nama', 'like', "%{$s}%"));
            });
        }

        $isFiltering = $request->filled('search') || 
                       ($request->filled('kategori') && $request->kategori !== 'semua') || 
                       $request->filled('lokasi') || 
                       ($request->filled('status') && $request->status !== 'semua');
        
        $highlightDates = clone $query;
        $highlightDates = $highlightDates->pluck('tanggal_jadwal')
                                       ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
                                       ->toArray();

        $perPage = $request->get('per_page', 8);
        $jadwal  = $query->orderBy('tanggal_jadwal')->paginate($perPage)->withQueryString();

        $statsQuery  = Jadwal::whereYear('tanggal_jadwal', $tahun)->whereMonth('tanggal_jadwal', $bln);
        
        $total       = (clone $statsQuery)->count();
        $selesai     = (clone $statsQuery)->where('status', 'selesai')->count();
        $jatuh_tempo = (clone $statsQuery)->where('status', 'pending')
                        ->whereBetween('tanggal_jadwal', [now(), now()->addDays(7)])->count();
        $terlambat   = (clone $statsQuery)->where('status', 'terlambat')->count();

        $kalender = Jadwal::whereYear('tanggal_jadwal', $tahun)
            ->whereMonth('tanggal_jadwal', $bln)
            ->selectRaw('tanggal_jadwal, status, COUNT(*) as jumlah')
            ->groupBy('tanggal_jadwal', 'status')
            ->get()
            ->groupBy(fn($j) => Carbon::parse($j->tanggal_jadwal)->format('Y-m-d'));

        $kategori_list = KategoriBarang::where('is_active', true)->orderBy('nama')->get();
        $waktu_list    = ManajemenWaktu::orderBy('satuan')->orderBy('interval')->get(); 

        $lokasi_list = \App\Models\Lokasi::where('is_active', true)->orderBy('nama')->get();
        $barang_tanpa_lokasi = Barang::where('is_active', true)->whereNull('lokasi_id')->count();

        return view('admin.jadwal.index', compact(
            'jadwal', 'total', 'selesai', 'jatuh_tempo', 'terlambat',
            'kalender', 'bulan', 'tahun', 'bln',
            'kategori_list', 'lokasi_list',
            'barang_tanpa_lokasi', 'waktu_list',
            'isFiltering', 'highlightDates'
        ));
    }

    public function create()
    {
        $barang_list = Barang::with(['kategori', 'lokasiRelasi'])
            ->where('is_active', true)
            ->whereNotNull('lokasi_id')
            ->whereNotNull('kategori_id')
            ->orderBy('kode_barang')
            ->get();

        $barang_belum_siap = Barang::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('lokasi_id')->orWhereNull('kategori_id');
            })
            ->count();

        $kategori_list = KategoriBarang::where('is_active', true)->orderBy('nama')->get();
        
        $user_list = User::with('spesialisasi')->where('role', 'user')->where('is_active', true)->get();
        
        $waktu_list = ManajemenWaktu::orderBy('satuan')->orderBy('interval')->get(); 

        return view('admin.jadwal.create', compact('barang_list', 'kategori_list', 'barang_belum_siap', 'user_list', 'waktu_list'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barang_id'     => 'required',
            'user_id'       => 'required|array', 
            'user_id.*'     => 'exists:users,id',
            'tanggal_mulai' => 'required|date',
            'frekuensi'     => 'required|exists:manajemen_waktu,nama',
            'jam_mulai'     => 'nullable|date_format:H:i',
            'jam_selesai'   => 'nullable|date_format:H:i|after:jam_mulai',
            'keterangan'    => 'nullable|string',
        ]);

        $barangId = is_array($validated['barang_id']) ? $validated['barang_id'][0] : $validated['barang_id'];
        $barang = Barang::findOrFail($barangId);

        if (!$barang->lokasi_id || !$barang->kategori_id) {
            return redirect()->back()->withInput()->with('error', "Barang {$barang->kode_barang} belum siap (cek lokasi/kategori).");
        }

        $masterWaktu = ManajemenWaktu::where('nama', $validated['frekuensi'])->first();

        $tglMulai   = Carbon::parse($validated['tanggal_mulai']);
        $tglAkhir   = Carbon::parse($validated['tanggal_mulai'])->endOfYear(); 
        $totalDibuat = 0;

        foreach ($validated['user_id'] as $uid) {
            $tglCurrent = $tglMulai->copy();
            $dibuatUntukPetugasIni = 0;

            while ($tglCurrent->lte($tglAkhir)) {
                $sudahAda = Jadwal::where('barang_id', $barang->id)
                    ->where('user_id', $uid)
                    ->where('status', 'pending')
                    ->whereDate('tanggal_jadwal', $tglCurrent->format('Y-m-d'))
                    ->exists();

                if (!$sudahAda) {
                    Jadwal::create([
                        'judul'          => 'Pengecekan ' . $barang->nama_barang,
                        'barang_id'      => $barang->id,
                        'user_id'        => $uid, 
                        'tanggal_mulai'  => $tglMulai,
                        'tanggal_jadwal' => $tglCurrent->copy(),
                        'jam_mulai'      => $validated['jam_mulai'] ?? null,
                        'jam_selesai'    => $validated['jam_selesai'] ?? null,
                        'frekuensi'      => $validated['frekuensi'],
                        'status'         => 'pending',
                        'keterangan'     => $validated['keterangan'] ?? null,
                    ]);
                    $totalDibuat++;
                    $dibuatUntukPetugasIni++;
                }

                match ($masterWaktu->satuan) {
                    'hari'   => $tglCurrent->addDays($masterWaktu->interval),
                    'minggu' => $tglCurrent->addWeeks($masterWaktu->interval),
                    'bulan'  => $tglCurrent->addMonths($masterWaktu->interval),
                    'tahun'  => $tglCurrent->addYears($masterWaktu->interval),
                };
            }

            if ($dibuatUntukPetugasIni > 0) {
                $tglPertama = Carbon::parse($validated['tanggal_mulai'])->translatedFormat('d F Y');
                Notification::create([
                    'user_id' => $uid,
                    'judul' => 'Tugas Baru: ' . $barang->nama_barang,
                    'pesan' => "Admin menugaskan Anda {$dibuatUntukPetugasIni} jadwal pengecekan untuk {$barang->nama_barang} " .
                             "di " . ($barang->lokasiRelasi->nama ?? '???') . ". Mulai dari {$tglPertama}.",
                    'tipe' => 'jadwal_baru',
                    'link' => 'user.jadwal.index',
                ]);
            }
        }

        return redirect()->route('admin.jadwal.index')->with('success', "{$totalDibuat} jadwal berhasil dibuat hingga akhir tahun.");
    }

    public function show(Jadwal $jadwal)
    {
        $jadwal->load(['barang.qrCode', 'barang.kategori', 'barang.lokasiRelasi', 'pengecekan.user', 'user']);
        $jadwal_berikutnya = $jadwal->tanggalBerikutnya();

        $petugas_sesuai = User::where('role', 'user')
            ->where('is_active', true)
            ->whereHas('spesialisasi', function($q) use ($jadwal) {
                // Null-safe guard in case barang is missing
                $kategori_id = $jadwal->barang?->kategori_id ?? 0;
                $q->where('kategori_barang_id', $kategori_id);
            })->get();

        $barang_satu_lokasi = ($jadwal->barang && $jadwal->barang->lokasi_id) ? Barang::where('lokasi_id', $jadwal->barang->lokasi_id)->where('id', '!=', $jadwal->barang_id)->where('is_active', true)->with('kategori')->get() : collect();
        $riwayat = Jadwal::where('barang_id', $jadwal->barang_id)->orderByDesc('tanggal_jadwal')->take(10)->get();

        return view('admin.jadwal.show', compact('jadwal', 'jadwal_berikutnya', 'riwayat', 'petugas_sesuai', 'barang_satu_lokasi'));
    }

    public function edit(Jadwal $jadwal)
    {
        $barang_list = Barang::with(['kategori', 'lokasiRelasi'])->where('is_active', true)->whereNotNull('lokasi_id')->whereNotNull('kategori_id')->orderBy('kode_barang')->get();
        
        $user_list = User::with('spesialisasi')->where('role', 'user')->where('is_active', true)->get();
        
        $kategori_list = KategoriBarang::where('is_active', true)->get();
        $waktu_list = ManajemenWaktu::orderBy('satuan')->orderBy('interval')->get(); 

        return view('admin.jadwal.edit', compact('jadwal', 'barang_list', 'user_list', 'kategori_list', 'waktu_list'));
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $validated = $request->validate([
            'barang_id'      => 'required|exists:barang,id',
            'user_id'        => 'required|exists:users,id', 
            'tanggal_jadwal' => 'required|date',
            'frekuensi'      => 'required|exists:manajemen_waktu,nama', 
            'status'         => 'required|in:pending,selesai,terlambat',
            'keterangan'     => 'nullable|string',
        ]);

        $barang = Barang::findOrFail($validated['barang_id']);
        if (!$barang->lokasi_id) return redirect()->back()->withInput()->with('error', "Barang {$barang->kode_barang} belum memiliki lokasi.");

        $validated['judul']  = 'Pengecekan ' . $barang->nama_barang;
        $jadwal->update($validated);

        return redirect()->route('admin.jadwal.show', $jadwal->id)->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();
        return redirect()
            ->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil dihapus.');
    }

    public function perpanjangTahunDepan(Request $request)
    {
        $tahunTarget = now()->year + 1; 
        $tglBatas    = Carbon::create($tahunTarget, 12, 31); 

        $barangList = Barang::where('is_active', true)->whereNotNull('lokasi_id')->whereNotNull('kategori_id')->get();

        $totalDibuat = 0;
        $totalSkip   = 0;

        foreach ($barangList as $barang) {
            $lastJadwal = Jadwal::where('barang_id', $barang->id)
                                ->orderBy('tanggal_jadwal', 'desc')
                                ->first();

            if (!$lastJadwal) {
                $totalSkip++;
                continue;
            }

            $masterWaktu = ManajemenWaktu::where('nama', $lastJadwal->frekuensi)->first();
            if(!$masterWaktu) continue;

            $tglCurrent = Carbon::parse($lastJadwal->tanggal_jadwal);

            match ($masterWaktu->satuan) {
                'hari'   => $tglCurrent->addDays($masterWaktu->interval),
                'minggu' => $tglCurrent->addWeeks($masterWaktu->interval),
                'bulan'  => $tglCurrent->addMonths($masterWaktu->interval),
                'tahun'  => $tglCurrent->addYears($masterWaktu->interval),
            };

            while ($tglCurrent->lte($tglBatas)) {
                $sudahAda = Jadwal::where('barang_id', $barang->id)
                    ->whereDate('tanggal_jadwal', $tglCurrent->format('Y-m-d'))
                    ->exists();

                if (!$sudahAda) {
                    Jadwal::create([
                        'judul'          => 'Pengecekan ' . $barang->nama_barang,
                        'barang_id'      => $barang->id,
                        'user_id'        => $lastJadwal->user_id, 
                        'tanggal_mulai'  => $tglCurrent->copy(),
                        'tanggal_jadwal' => $tglCurrent->copy(),
                        'frekuensi'      => $lastJadwal->frekuensi,
                        'status'         => 'pending',
                        'jam_mulai'      => $lastJadwal->jam_mulai,
                        'jam_selesai'    => $lastJadwal->jam_selesai,
                        'keterangan'     => $lastJadwal->keterangan,
                    ]);
                    $totalDibuat++;
                }

                match ($masterWaktu->satuan) {
                    'hari'   => $tglCurrent->addDays($masterWaktu->interval),
                    'minggu' => $tglCurrent->addWeeks($masterWaktu->interval),
                    'bulan'  => $tglCurrent->addMonths($masterWaktu->interval),
                    'tahun'  => $tglCurrent->addYears($masterWaktu->interval),
                };
            }
        }

        $msg = "Ajaib! ✨ {$totalDibuat} jadwal berhasil diperpanjang hingga akhir tahun {$tahunTarget}.";
        if ($totalSkip > 0) $msg .= " ({$totalSkip} barang dilewati karena belum punya riwayat jadwal).";

        return redirect()->route('admin.jadwal.index')->with('success', $msg);
    }

    public function selesai(Jadwal $jadwal)
    {
        $jadwal->update(['status' => 'selesai']);
        $jadwalBaru = $jadwal->buatJadwalBerikutnya();

        if ($jadwalBaru) {
            $tgl = Carbon::parse($jadwalBaru->tanggal_jadwal)->translatedFormat('d F Y');
            return redirect()->route('admin.jadwal.index')->with('success', "Jadwal selesai. Jadwal berikutnya otomatis dibuat untuk {$tgl}.");
        }
        return redirect()->route('admin.jadwal.index')->with('success', "Jadwal selesai. Jadwal berikutnya tidak dibuat otomatis.");
    }

    public function kalenderDetail(Request $request)
    {
        $request->validate(['tanggal' => 'required|date']);
        $jadwal = Jadwal::with(['barang.kategori', 'barang.lokasiRelasi'])
            ->whereDate('tanggal_jadwal', $request->tanggal)
            ->orderBy('status')
            ->get()
            ->map(function ($j) {
                return [
                    'id'        => $j->id,
                    'nama'      => $j->barang?->nama_barang ?? 'Barang Terhapus',
                    'kode'      => $j->barang?->kode_barang ?? '-',
                    'kategori'  => $j->barang?->kategori?->nama ?? '—',
                    'warna'     => $j->barang?->kategori?->warna ?? '#94a3b8',
                    'lokasi'    => $j->barang?->lokasiRelasi?->nama ?? $j->barang?->lokasi ?? '—',
                    'status'    => $j->status,
                    'frekuensi' => $j->frekuensiLabel(),
                    'jam'       => $j->jamLabel(),
                    'url'       => route('admin.jadwal.show', $j->id),
                ];
            });
        return response()->json($jadwal);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:jadwal,id',
        ]);

        $count = Jadwal::whereIn('id', $request->ids)->where('status', '!=', 'selesai')->delete();
        return redirect()->route('admin.jadwal.index')->with('success', "{$count} jadwal berhasil dihapus.");
    }

    public function getJadwalForDelete(Request $request)
    {
        $query = Jadwal::with(['barang.kategori', 'barang.lokasiRelasi'])
            ->where('status', '!=', 'selesai'); 

        if ($request->filled('kategori') && $request->kategori !== 'semua') {
            $query->whereHas('barang', fn($q) => $q->where('kategori_id', $request->kategori));
        }

        if ($request->filled('lokasi') && $request->lokasi !== 'semua') {
            $query->whereHas('barang', fn($q) => $q->where('lokasi_id', $request->lokasi));
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('judul', 'like', "%{$s}%")
                  ->orWhereHas('barang', fn($q2) => $q2
                      ->where('nama_barang', 'like', "%{$s}%")
                      ->orWhere('kode_barang', 'like', "%{$s}%"));
            });
        }

        $jadwalList = $query->orderBy('tanggal_jadwal', 'asc')->get()->map(function ($j) {
            return [
                'id'          => $j->id,
                'kode_barang' => $j->barang?->kode_barang ?? '-',
                'nama_barang' => $j->barang?->nama_barang ?? 'Barang Terhapus',
                'kategori'    => $j->barang?->kategori?->nama ?? '-',
                'lokasi'      => $j->barang?->lokasiRelasi?->nama ?? $j->barang?->lokasi ?? '-',
                'tanggal'     => \Carbon\Carbon::parse($j->tanggal_jadwal)->translatedFormat('d M Y'),
                'status'      => $j->status,
            ];
        });

        return response()->json($jadwalList);
    }

    public function bulkDeleteAdvanced(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:jadwal,id',
        ]);

        $deletedCount = Jadwal::whereIn('id', $request->ids)
            ->where('status', '!=', 'selesai')
            ->delete();

        return redirect()->route('admin.jadwal.index')
            ->with('success', "Berhasil menghapus {$deletedCount} jadwal lintas periode!");
    }
}