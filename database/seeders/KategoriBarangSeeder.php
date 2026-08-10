<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriBarangSeeder extends Seeder
{
    public function run(): void
    {
        // Migrasi data lama AC dan APAR ke tabel kategori_barang
        $kategori = [
            [
                'nama'      => 'AC',
                'kode'      => 'AC',
                'ikon'      => 'ac',
                'warna'     => '#2563eb',
                'deskripsi' => 'Air Conditioner — Sistem pendingin udara',
                'is_active' => true,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'nama'      => 'APAR',
                'kode'      => 'APAR',
                'ikon'      => 'apar',
                'warna'     => '#ef4444',
                'deskripsi' => 'Alat Pemadam Api Ringan',
                'is_active' => true,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
        ];

        foreach ($kategori as $k) {
            // insertOrIgnore agar tidak error jika sudah ada
            DB::table('kategori_barang')->insertOrIgnore($k);
        }
    }
}