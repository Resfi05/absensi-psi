<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\KategoriBarang;

class PetugasSeeder extends Seeder
{
    public function run(): void
    {
        $ac   = KategoriBarang::where('kode', 'AC')->first();
        $apar = KategoriBarang::where('kode', 'APAR')->first();

        $petugas = [
            [
                'name'            => 'Budi Santoso',
                'username'        => 'budi.ac',
                'email'           => 'budi.ac@padmasoode.com',
                'password'        => Hash::make('password123'),
                'role'            => 'user',
                'spesialisasi_id' => $ac?->id,
                'no_hp'           => '081234567801',
                'is_active'       => true,
            ],
            [
                'name'            => 'Apin Wijaya',
                'username'        => 'apin.apar',
                'email'           => 'apin.apar@padmasoode.com',
                'password'        => Hash::make('password123'),
                'role'            => 'user',
                'spesialisasi_id' => $apar?->id,
                'no_hp'           => '081234567802',
                'is_active'       => true,
            ],
            [
                'name'            => 'Sari Dewi',
                'username'        => 'sari.ac',
                'email'           => 'sari.ac@padmasoode.com',
                'password'        => Hash::make('password123'),
                'role'            => 'user',
                'spesialisasi_id' => $ac?->id,
                'no_hp'           => '081234567803',
                'is_active'       => true,
            ],
        ];

        foreach ($petugas as $p) {
            User::firstOrCreate(['username' => $p['username']], $p);
        }
    }
}