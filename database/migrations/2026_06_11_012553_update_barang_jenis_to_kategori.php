<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: tambah kolom kategori_id
        Schema::table('barang', function (Blueprint $table) {
            $table->foreignId('kategori_id')
                  ->nullable()
                  ->after('jenis_barang')
                  ->constrained('kategori_barang')
                  ->onDelete('set null');
        });

        // Step 2: isi kategori_id berdasarkan jenis_barang lama
        // (dijalankan setelah seeder KategoriBarangSeeder)
        $ac   = DB::table('kategori_barang')->where('kode', 'AC')->first();
        $apar = DB::table('kategori_barang')->where('kode', 'APAR')->first();

        if ($ac) {
            DB::table('barang')->where('jenis_barang', 'AC')->update(['kategori_id' => $ac->id]);
        }
        if ($apar) {
            DB::table('barang')->where('jenis_barang', 'APAR')->update(['kategori_id' => $apar->id]);
        }

        // Barang "Lainnya" → kategori_id tetap null dulu sampai admin assign manual
    }

    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
            $table->dropColumn('kategori_id');
        });
    }
};