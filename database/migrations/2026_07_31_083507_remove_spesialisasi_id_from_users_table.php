<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus foreign key (jika sebelumnya ada) lalu hapus kolomnya
            $table->dropForeign(['spesialisasi_id']);
            $table->dropColumn('spesialisasi_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kembalikan kolom jika sewaktu-waktu di-rollback
            $table->foreignId('spesialisasi_id')->nullable()->constrained('kategori_barang')->nullOnDelete();
        });
    }
};