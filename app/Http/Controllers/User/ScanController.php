<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\QrCode;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ScanController extends Controller
{
    /**
     * Halaman kamera scan QR (dari bottom nav / dashboard)
     */
    public function index(Request $request)
    {
        $jadwalTarget = $request->get('jadwal');
        // Kalau ada $barang dari validasi, tidak perlu — ini halaman kamera saja
        return view('user.scan.index', ['jadwalTarget' => $jadwalTarget, 'barang' => null]);
    }

    /**
     * Tahap 1: Validasi token QR → tampilkan halaman portal pilihan
     */
    public function validasi(Request $request, string $token)
    {
        $qrCode = QrCode::where('qr_token', $token)
            ->where('is_active', true)
            ->first();

        if (!$qrCode) {
            return view('user.scan.gagal', [
                'pesan' => 'QR Code tidak valid atau tidak ditemukan.',
            ]);
        }

        $barang = $qrCode->barang;

        if (!$barang || !$barang->is_active) {
            return view('user.scan.gagal', [
                'pesan' => "Barang ini berstatus NONAKTIF dan tidak bisa diakses.",
            ]);
        }

        // Simpan token di session supaya prosesPetugas bisa pakai tanpa kirim ulang via URL
        session(['scan_token_aktif' => $token]);

        $jadwalTarget = $request->get('jadwal');

        // Tampilkan halaman portal pilihan (Petugas / Auditor)
        return view('user.scan.index', compact('barang', 'jadwalTarget'));
    }

    /**
     * Tahap 2: Petugas klik tombol "Petugas (Checklist)" di halaman portal
     */
    public function prosesPetugas(Request $request)
    {
        // Ambil token dari session (disimpan saat validasi)
        $token = session('scan_token_aktif');

        if (!$token) {
            return view('user.scan.gagal', [
                'pesan' => 'Sesi scan tidak valid. Silakan scan ulang QR Code.',
            ]);
        }

        $qrCode = QrCode::where('qr_token', $token)
            ->where('is_active', true)
            ->first();

        if (!$qrCode) {
            return view('user.scan.gagal', ['pesan' => 'QR Code tidak valid.']);
        }

        $user   = Auth::user();
        $barang = $qrCode->barang;

        $jadwalTarget = $request->get('jadwal');

        // Kasus 1: Masuk dari klik "Kerjakan" di detail jadwal
        if ($jadwalTarget) {
            $jadwal = Jadwal::find($jadwalTarget);

            if (!$jadwal) {
                return view('user.scan.gagal', ['pesan' => 'Jadwal tujuan tidak ditemukan.']);
            }

            if ($jadwal->barang_id !== $barang->id) {
                return view('user.scan.gagal', [
                    'pesan' => "QR Code ini untuk \"{$barang->nama_barang}\", bukan untuk jadwal yang Anda pilih.",
                ]);
            }

            return $this->lanjutKeJadwal($jadwal);
        }

        // Kasus 2: Scan bebas — cari jadwal pending yang sesuai
        $jadwal = Jadwal::where('barang_id', $barang->id)
            ->where('status', 'pending')
            ->where(function ($query) use ($user, $barang) {
                $query->where('user_id', $user->id);
                if ($user->spesialisasi_id === $barang->kategori_id) {
                    $query->orWhereNull('user_id');
                }
            })
            ->orderByRaw("CASE WHEN jenis_jadwal = 'perbaikan' THEN 1 ELSE 2 END")
            ->orderBy('tanggal_jadwal')
            ->first();

        if (!$jadwal) {
            if ($barang->kategori_id !== $user->spesialisasi_id) {
                return view('user.scan.gagal', [
                    'pesan' => "Barang ini bukan spesialisasi Anda dan tidak ada penugasan khusus.",
                ]);
            }
            return view('user.scan.gagal', [
                'pesan' => "Tidak ada jadwal aktif untuk {$barang->nama_barang} saat ini.",
            ]);
        }

        return $this->lanjutKeJadwal($jadwal);
    }

    /**
     * Cek gembok waktu & terbitkan tiket scan
     */
    private function lanjutKeJadwal(Jadwal $jadwal)
    {
        $tglJadwal = \Carbon\Carbon::parse($jadwal->tanggal_jadwal)->startOfDay();

        if ($tglJadwal->isFuture()) {
            $tipeLabel = ($jadwal->jenis_jadwal ?? 'pengecekan') === 'perbaikan' ? 'perbaikan' : 'pengecekan';
            return view('user.scan.gagal', [
                'pesan'      => "Tugas {$tipeLabel} ini baru bisa dikerjakan pada {$tglJadwal->translatedFormat('d F Y')}.",
                'belumWaktu' => true,
                'jadwal'     => $jadwal,
            ]);
        }

        if ($jadwal->status === 'selesai') {
            return view('user.scan.gagal', [
                'pesan' => 'Tugas ini sudah selesai dikerjakan sebelumnya.',
            ]);
        }

        // Terbitkan tiket aman di session
        $ticket = Str::random(40);
        session(["scan_ticket_{$jadwal->id}" => [
            'ticket'     => $ticket,
            'expires_at' => now()->addMinutes(30)->timestamp,
        ]]);

        // Hapus token scan dari session (sudah dipakai)
        session()->forget('scan_token_aktif');

        return redirect()->route('user.pengecekan.mulai', [
            'jadwal' => $jadwal->id,
            'ticket' => $ticket,
        ]);
    }
}