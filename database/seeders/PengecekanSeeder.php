<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jadwal;
use App\Models\User;
use App\Models\Pengecekan;
use Carbon\Carbon;

class PengecekanSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua jadwal yang barangnya sudah punya kategori (siap dicek)
        $jadwalList = Jadwal::with('barang.kategori')
            ->whereHas('barang', fn($q) => $q->whereNotNull('kategori_id')->whereNotNull('lokasi_id'))
            ->get();

        if ($jadwalList->isEmpty()) {
            $this->command->warn('Tidak ada jadwal dengan barang lengkap (lokasi & kategori). Seeder pengecekan dilewati.');
            return;
        }

        $statuses = ['aman', 'aman', 'aman', 'perlu_tindakan', 'tertunda'];

        $checklistContoh = [
            'AC' => [
                ['item' => 'Filter udara bersih', 'checked' => true],
                ['item' => 'Suhu sesuai setting', 'checked' => true],
                ['item' => 'Tidak ada suara aneh', 'checked' => true],
                ['item' => 'Kondensasi normal', 'checked' => true],
            ],
            'APAR' => [
                ['item' => 'Tabung tidak bocor', 'checked' => true],
                ['item' => 'Tekanan jarum di zona hijau', 'checked' => true],
                ['item' => 'Segel masih utuh', 'checked' => true],
                ['item' => 'Tanggal kadaluarsa masih jauh', 'checked' => true],
            ],
        ];

        $catatanRusak = [
            'Filter terlihat kotor, perlu pembersihan menyeluruh.',
            'Terdengar suara berdengung tidak normal saat unit menyala.',
            'Tekanan jarum sudah di zona merah, perlu isi ulang.',
            'Segel pengaman sudah rusak, perlu penggantian.',
            'Terdapat kebocoran kecil pada pipa saluran.',
        ];

        $count = 0;

        foreach ($jadwalList as $jadwal) {
            // Skip kalau sudah ada pengecekan untuk jadwal ini
            if (Pengecekan::where('jadwal_id', $jadwal->id)->exists()) {
                continue;
            }

            $kategoriId = $jadwal->barang->kategori_id;
            $kategoriNama = $jadwal->barang->kategori->nama ?? 'AC';

            // Cari petugas yang sesuai spesialisasi
            $petugas = User::where('role', 'user')
                ->where('is_active', true)
                ->where('spesialisasi_id', $kategoriId)
                ->inRandomOrder()
                ->first();

            if (!$petugas) {
                continue; // tidak ada petugas yang cocok, skip
            }

            $status = $statuses[array_rand($statuses)];
            $checklist = $checklistContoh[$kategoriNama] ?? $checklistContoh['AC'];

            // Kalau status perlu_tindakan, ubah salah satu item checklist jadi false
            if ($status === 'perlu_tindakan') {
                $idx = array_rand($checklist);
                $checklist[$idx]['checked'] = false;
            }

            $checkedAt = Carbon::now()->subDays(rand(0, 10))->subHours(rand(0, 12));

            Pengecekan::create([
                'jadwal_id'      => $jadwal->id,
                'user_id'        => $petugas->id,
                'checklist_data' => $status === 'tertunda' ? null : $checklist,
                'photo_before'   => $status !== 'tertunda' ? 'pengecekan/dummy_before.jpg' : null,
                'photo_after'    => $status === 'aman' ? 'pengecekan/dummy_after.jpg' : null,
                'notes'          => $status === 'perlu_tindakan' ? $catatanRusak[array_rand($catatanRusak)] : null,
                'status'         => $status,
                'checked_at'     => $status === 'tertunda' ? null : $checkedAt,
                'created_at'     => $checkedAt,
                'updated_at'     => $checkedAt,
            ]);

            $count++;
        }

        $this->command->info("{$count} data pengecekan dummy berhasil dibuat.");
    }
}