<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal', function (Blueprint $table) {
            // Jadikan user_id nullable — jadwal tidak wajib assign ke 1 petugas
            $table->foreignId('user_id')->nullable()->change();

            // Tambah tanggal_mulai sebagai referensi awal interval
            $table->date('tanggal_mulai')->nullable()->after('barang_id');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->dropColumn('tanggal_mulai');
        });
    }
};