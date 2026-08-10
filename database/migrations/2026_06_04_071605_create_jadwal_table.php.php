<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // petugas yang ditugaskan
            $table->foreignId('barang_id')->constrained('barang')->onDelete('cascade');
            $table->date('tanggal_jadwal');
            $table->enum('frekuensi', ['harian', 'mingguan', 'bulanan'])->default('bulanan');
            $table->enum('status', ['pending', 'selesai', 'terlambat'])->default('pending');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};