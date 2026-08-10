<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Barang;
use App\Models\ChecklistTemplate;
use App\Models\QrCode;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =====================
        // SEED USERS
        // =====================
        User::create([
            'name'     => 'Administrator',
            'username' => 'admin',
            'email'    => 'admin@padmasoode.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'no_hp'    => '081234567890',
            'is_active'=> true,
        ]);

        User::create([
            'name'     => 'Petugas Satu',
            'username' => 'petugas1',
            'email'    => 'petugas1@padmasoode.com',
            'password' => Hash::make('password'),
            'role'     => 'user',
            'no_hp'    => '081234567891',
            'is_active'=> true,
        ]);

        User::create([
            'name'     => 'Petugas Dua',
            'username' => 'petugas2',
            'email'    => 'petugas2@padmasoode.com',
            'password' => Hash::make('password'),
            'role'     => 'user',
            'no_hp'    => '081234567892',
            'is_active'=> true,
        ]);

        // =====================
        // SEED CHECKLIST TEMPLATE
        // =====================

        // Checklist untuk AC
        $checklistAC = [
            'Kondisi fisik unit AC',
            'Kebersihan filter',
            'Kondisi remote control',
            'Suhu pendinginan normal',
            'Kondisi selang pembuangan air',
            'Tidak ada suara berisik',
            'Kondisi kabel listrik',
        ];
        foreach ($checklistAC as $i => $item) {
            ChecklistTemplate::create([
                'jenis_barang' => 'AC',
                'nama_item'    => $item,
                'urutan'       => $i + 1,
            ]);
        }

        // Checklist untuk APAR
        $checklistAPAR = [
            'Kondisi tabung (tidak karat/bocor)',
            'Tekanan manometer (jarum di zona hijau)',
            'Kondisi selang dan nozzle',
            'Segel/pin pengaman masih terpasang',
            'Label kadaluarsa masih valid',
            'Posisi penempatan mudah dijangkau',
            'Kondisi bracket/gantungan',
        ];
        foreach ($checklistAPAR as $i => $item) {
            ChecklistTemplate::create([
                'jenis_barang' => 'APAR',
                'nama_item'    => $item,
                'urutan'       => $i + 1,
            ]);
        }

        // =====================
        // SEED SAMPLE BARANG
        // =====================
        $barangList = [
            ['kode_barang' => 'AC-001', 'nama_barang' => 'AC Ruang Kantor Lt.1', 'jenis_barang' => 'AC',   'lokasi' => 'Gedung A', 'gedung' => 'A', 'lantai' => '1', 'ruangan' => 'Kantor Utama'],
            ['kode_barang' => 'AC-002', 'nama_barang' => 'AC Ruang Meeting',      'jenis_barang' => 'AC',   'lokasi' => 'Gedung A', 'gedung' => 'A', 'lantai' => '2', 'ruangan' => 'Meeting Room'],
            ['kode_barang' => 'AC-003', 'nama_barang' => 'AC Ruang Server',       'jenis_barang' => 'AC',   'lokasi' => 'Gedung B', 'gedung' => 'B', 'lantai' => '1', 'ruangan' => 'Server Room'],
            ['kode_barang' => 'AP-001', 'nama_barang' => 'APAR Lobby Gedung A',   'jenis_barang' => 'APAR', 'lokasi' => 'Gedung A', 'gedung' => 'A', 'lantai' => '1', 'ruangan' => 'Lobby'],
            ['kode_barang' => 'AP-002', 'nama_barang' => 'APAR Tangga Darurat',   'jenis_barang' => 'APAR', 'lokasi' => 'Gedung A', 'gedung' => 'A', 'lantai' => '2', 'ruangan' => 'Tangga'],
            ['kode_barang' => 'AP-003', 'nama_barang' => 'APAR Gudang',           'jenis_barang' => 'APAR', 'lokasi' => 'Gedung B', 'gedung' => 'B', 'lantai' => '1', 'ruangan' => 'Gudang'],
        ];

        foreach ($barangList as $b) {
            $barang = Barang::create($b);

            // Buat QR code token untuk setiap barang
            QrCode::create([
                'barang_id'    => $barang->id,
                'qr_code_path' => 'qrcodes/' . $barang->kode_barang . '.png',
                'qr_token'     => Str::uuid(),
                'is_active'    => true,
            ]);
        }
    }
}