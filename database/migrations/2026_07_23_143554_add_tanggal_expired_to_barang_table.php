<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('barang', function (Blueprint $table) {
            // Menambahkan kolom tanggal_expired (boleh kosong/nullable untuk barang yang tidak punya expired)
            $table->date('tanggal_expired')->nullable()->after('kategori_id');
        });
    }

    public function down()
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropColumn('tanggal_expired');
        });
    }
};