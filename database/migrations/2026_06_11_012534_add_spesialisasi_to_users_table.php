<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // spesialisasi mengacu ke kategori_barang.id
            // nullable = admin tidak punya spesialisasi
            $table->foreignId('spesialisasi_id')
                  ->nullable()
                  ->after('role')
                  ->constrained('kategori_barang')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['spesialisasi_id']);
            $table->dropColumn('spesialisasi_id');
        });
    }
};