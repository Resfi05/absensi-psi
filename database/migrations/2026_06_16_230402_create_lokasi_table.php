<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lokasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique(); // "Gedung A - Lantai 1 - Ruang Meeting"
            $table->string('kode')->nullable(); // kode singkat opsional
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tambah kolom lokasi_id ke tabel barang
        Schema::table('barang', function (Blueprint $table) {
            $table->foreignId('lokasi_id')
                  ->nullable()
                  ->after('kategori_id')
                  ->constrained('lokasi')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropForeign(['lokasi_id']);
            $table->dropColumn('lokasi_id');
        });
        Schema::dropIfExists('lokasi');
    }
};