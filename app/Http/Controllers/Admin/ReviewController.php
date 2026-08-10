<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengecekan;
use App\Models\Jadwal;
use App\Models\KategoriBarang;
use App\Models\Lokasi;
use App\Models\ActivityLog;
use App\Models\User;         // 🔥 DITAMBAHKAN UNTUK NOTIFIKASI
use App\Models\Notification;   // 🔥 DITAMBAHKAN UNTUK NOTIFIKASI
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengecekan::with(['jadwal.barang.kategori', 'jadwal.barang.lokasiRelasi', 'user'])
            ->where('status', 'perlu_tindakan')
            ->whereNotNull('jadwal_id'); // abaikan data legacy tanpa jadwal

        if ($request->filled('kategori') && $request->kategori !== 'semua') {
            $query->whereHas('jadwal.barang', fn($q) => $q->where('kategori_id', $request->kategori));
        }
        if ($request->filled('lokasi') && $request->lokasi !== 'semua') {
            $query->whereHas('jadwal.barang', fn($q) => $q->where('lokasi_id', $request->lokasi));
        }
        if ($request->filled('status_tindak_lanjut') && $request->status_tindak_lanjut !== 'semua') {
            $query->where('status_tindak_lanjut', $request->status_tindak_lanjut);
        }
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('checked_at', [
                Carbon::parse($request->tanggal_mulai)->startOfDay(),
                Carbon::parse($request->tanggal_akhir)->endOfDay(),
            ]);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('jadwal.barang', fn($q2) => $q2
                    ->where('nama_barang', 'like', "%{$s}%")
                    ->orWhere('kode_barang', 'like', "%{$s}%"))
                    ->orWhereHas('user', fn($q3) => $q3->where('name', 'like', "%{$s}%"));
            });
        }

        $perPage = $request->get('per_page', 10);
        $temuan  = $query->latest('checked_at')->paginate($perPage)->withQueryString();

        $allTemuan = Pengecekan::where('status', 'perlu_tindakan')->whereNotNull('jadwal_id');

        $totalTemuan       = (clone $allTemuan)->count();
        $menungguReview    = (clone $allTemuan)->where('status_tindak_lanjut', 'menunggu')->count();
        $sedangDitangani   = (clone $allTemuan)->where('status_tindak_lanjut', 'ditangani')->count();
        $selesaiDiperbaiki = (clone $allTemuan)->where('status_tindak_lanjut', 'selesai')->count();

        $kategori_list = KategoriBarang::where('is_active', true)->orderBy('nama')->get();
        $lokasi_list   = Lokasi::where('is_active', true)->orderBy('nama')->get();

        return view('admin.review.index', compact(
            'temuan', 'totalTemuan', 'menungguReview', 'sedangDitangani', 'selesaiDiperbaiki',
            'kategori_list', 'lokasi_list'
        ));
    }

    public function show(Pengecekan $pengecekan)
    {
        if (!$pengecekan->jadwal || !$pengecekan->jadwal->barang) {
            return redirect()
                ->route('admin.review.index')
                ->with('error', 'Data pengecekan ini tidak valid (jadwal/barang tidak ditemukan).');
        }

        $pengecekan->load(['jadwal.barang.kategori', 'jadwal.barang.lokasiRelasi', 'user']);

        $riwayatLain = Pengecekan::whereNotNull('jadwal_id')
            ->where('id', '!=', $pengecekan->id)
            ->whereHas('jadwal', fn($q) => $q->where('barang_id', $pengecekan->jadwal->barang_id))
            ->where('status', 'perlu_tindakan')
            ->latest('checked_at')
            ->take(5)
            ->get();

        return view('admin.review.show', compact('pengecekan', 'riwayatLain'));
    }

    public function tindakLanjuti(Request $request, Pengecekan $pengecekan)
    {
        if (!$pengecekan->jadwal || !$pengecekan->jadwal->barang) {
            return redirect()->route('admin.review.index')
                ->with('error', 'Data tidak valid, tidak bisa ditindaklanjuti.');
        }

        $request->validate([
            'tanggal_perbaikan' => 'required|date|after_or_equal:today',
        ]);

        $barang = $pengecekan->jadwal->barang;

        // Buat jadwal perbaikan dengan link ke pengecekan asal
        $jadwalPerbaikan = Jadwal::create([
            'judul'                => 'Perbaikan ' . $barang->nama_barang,
            'barang_id'            => $barang->id,
            'user_id'              => null,
            'tanggal_mulai'        => $request->tanggal_perbaikan,
            'tanggal_jadwal'       => $request->tanggal_perbaikan,
            'frekuensi'            => 'harian',
            'status'               => 'pending',
            'keterangan'           => 'Jadwal perbaikan dari temuan: ' . ($pengecekan->notes ?? 'Tidak ada catatan'),
            'jenis_jadwal'         => 'perbaikan',
            'parent_pengecekan_id' => $pengecekan->id,
        ]);

        // Update status tindak lanjut pengecekan asal
        $pengecekan->update(['status_tindak_lanjut' => 'ditangani']);

        // Catat log aktivitas
        $this->logTindakLanjut(
            $pengecekan,
            "membuat jadwal perbaikan untuk {$barang->kode_barang} ({$barang->nama_barang}) pada " .
            Carbon::parse($request->tanggal_perbaikan)->translatedFormat('d F Y')
        );

        // ── Notifikasi ke petugas yang spesialisasinya cocok ──
        $tglFormat = Carbon::parse($request->tanggal_perbaikan)->translatedFormat('d F Y');

        // FIX: Pakai nama tabel pivot secara eksplisit agar tidak ambigu
        $petugasList = User::where('role', 'user')
            ->where('is_active', true)
            ->whereHas('spesialisasi', function($q) use ($barang) {
                $q->where('kategori_barang_user.kategori_barang_id', $barang->kategori_id);
            })
            ->get();

        foreach ($petugasList as $petugas) {
            Notification::kirim(
    userId: $petugas->id,
    judul:  '🔧 Tugas Perbaikan: ' . $barang->nama_barang,
    pesan:  "Terdapat temuan kerusakan pada {$barang->nama_barang} di " .
            ($barang->lokasiRelasi->nama ?? 'lokasi tidak diketahui') .
            ". Segera lakukan tindak lanjut perbaikan mulai tanggal {$tglFormat}.",
    tipe:   'tindak_lanjut',
    link:   'user.scan',
    linkId: null,
);
        }

        return redirect()
            ->route('admin.review.show', $pengecekan->id)
            ->with('success', "Jadwal perbaikan untuk {$barang->kode_barang} berhasil dibuat untuk tanggal {$tglFormat}. Notifikasi telah dikirim ke " . count($petugasList) . " teknisi.");
    }

    public function abaikan(Pengecekan $pengecekan)
    {
        if (!$pengecekan->jadwal || !$pengecekan->jadwal->barang) {
            return redirect()->route('admin.review.index')
                ->with('error', 'Data tidak valid.');
        }

        $barang = $pengecekan->jadwal->barang;
        $pengecekan->update(['status_tindak_lanjut' => 'diabaikan']);

        $this->logTindakLanjut($pengecekan, "menandai temuan {$barang->kode_barang} sebagai diabaikan (false alarm)");

        return redirect()
            ->route('admin.review.index')
            ->with('success', 'Temuan ditandai sebagai diabaikan (false alarm).');
    }

    public function selesaikan(Pengecekan $pengecekan)
    {
        if (!$pengecekan->jadwal || !$pengecekan->jadwal->barang) {
            return redirect()->route('admin.review.index')
                ->with('error', 'Data tidak valid.');
        }

        $barang = $pengecekan->jadwal->barang;
        $pengecekan->update(['status_tindak_lanjut' => 'selesai']);

        $this->logTindakLanjut($pengecekan, "menandai temuan {$barang->kode_barang} selesai diperbaiki");

        return redirect()
            ->route('admin.review.index')
            ->with('success', 'Temuan ditandai selesai diperbaiki.');
    }

    private function logTindakLanjut(Pengecekan $pengecekan, string $aksiText): void
    {
        $user = Auth::user();

        ActivityLog::create([
            'user_id'    => $user?->id,
            'aksi'       => 'tindak_lanjut',
            'model_type' => Pengecekan::class,
            'model_id'   => $pengecekan->id,
            'deskripsi'  => ($user?->name ?? 'Sistem') . " {$aksiText}",
            'ip_address' => request()->ip(),
        ]);
    }
}