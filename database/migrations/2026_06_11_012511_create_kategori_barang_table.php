<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_barang', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();        // AC, APAR, Mesin, dll
            $table->string('kode')->unique();        // AC, APAR, MSN — untuk kode otomatis
            $table->string('ikon')->nullable();      // nama ikon svg / emoji
            $table->string('warna')->default('#2563eb'); // warna badge hex
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_barang');
    }
};