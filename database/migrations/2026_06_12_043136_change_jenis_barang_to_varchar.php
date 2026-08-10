<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            // Ubah dari ENUM ke VARCHAR agar bisa terima kategori apapun
            $table->string('jenis_barang')->change();
        });
    }

    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->enum('jenis_barang', ['AC', 'APAR', 'Lainnya'])->change();
        });
    }
};