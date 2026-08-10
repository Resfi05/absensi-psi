<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;

class HapusNotifikasiLama extends Command
{
    protected $signature   = 'notifikasi:hapus-lama';
    protected $description = 'Hapus semua notifikasi yang sudah lebih dari 7 hari';

    public function handle(): void
    {
        $jumlah = Notification::where('created_at', '<', now()->subDays(7))->delete();

        $this->info("{$jumlah} notifikasi lama berhasil dihapus.");
    }
}