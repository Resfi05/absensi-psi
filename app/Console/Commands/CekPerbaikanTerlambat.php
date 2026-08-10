<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jadwal;
use App\Models\Pengecekan;
use App\Models\Notification;
use Carbon\Carbon;

class CekPerbaikanTerlambat extends Command
{
    // Nama perintah yang akan dipanggil oleh sistem
    protected $signature = 'perbaikan:cek-terlambat';

    // Deskripsi perintah
    protected $description = 'Cek jadwal perbaikan yang lewat 1 hari dan ubah statusnya jadi terlambat';

    public function handle()
    {
        // 1. Cari jadwal perbaikan yang statusnya masih 'pending' padahal tanggalnya sudah lewat (kemarin)
        $jadwals = Jadwal::with('barang')
            ->where('jenis_jadwal', 'perbaikan')
            ->where('status', 'pending')
            ->whereDate('tanggal_jadwal', '<', Carbon::today())
            ->get();

        $count = 0;
        foreach ($jadwals as $jadwal) {
            // 2. Ubah status Jadwal itu sendiri menjadi terlambat (agar merah di kalender)
            $jadwal->update(['status' => 'terlambat']);

            // 3. Ubah status_tindak_lanjut di Laporan Pengecekan induknya
            if ($jadwal->parent_pengecekan_id) {
                Pengecekan::where('id', $jadwal->parent_pengecekan_id)
                    ->whereIn('status_tindak_lanjut', ['menunggu', 'ditangani'])
                    ->update(['status_tindak_lanjut' => 'terlambat']);
            }

            // 4. Kirim Surat Peringatan (Notifikasi) ke Petugas yang menunda pekerjaan
            if (class_exists(Notification::class)) {
                Notification::create([
                    'user_id' => $jadwal->user_id,
                    'judul'   => '🚨 Peringatan: Perbaikan Terlambat!',
                    'pesan'   => "Tugas perbaikan {$jadwal->barang->nama_barang} telah melewati batas waktu 1 hari. Harap segera diselesaikan!",
                    'tipe'    => 'peringatan', 
                    'link'    => 'user.jadwal.index',
                ]);
            }
            $count++;
        }

        $this->info("Berhasil memproses {$count} jadwal perbaikan yang terlambat.");
    }
}