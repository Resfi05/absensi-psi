<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('riwayat_barangs', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke ID barang
            $table->foreignId('barang_id')->constrained('barang')->cascadeOnDelete();
            
            // Relasi ke ID lokasi (bisa null jika lokasi dihapus master-nya)
            $table->foreignId('lokasi_lama_id')->nullable()->constrained('lokasi')->nullOnDelete();
            $table->foreignId('lokasi_baru_id')->nullable()->constrained('lokasi')->nullOnDelete();
            
            // Simpan status aktif / non-aktif
            $table->boolean('status_lama')->default(true);
            $table->boolean('status_baru')->default(true);
            
            // Keterangan tambahan (opsional)
            $table->text('keterangan')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('riwayat_barangs');
    }
};