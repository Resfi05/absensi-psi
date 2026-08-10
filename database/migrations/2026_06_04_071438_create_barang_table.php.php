<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->enum('jenis_barang', ['AC', 'APAR', 'Lainnya']);
            $table->string('lokasi');
            $table->string('gedung')->nullable();
            $table->string('lantai')->nullable();
            $table->string('ruangan')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('foto')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
}; 