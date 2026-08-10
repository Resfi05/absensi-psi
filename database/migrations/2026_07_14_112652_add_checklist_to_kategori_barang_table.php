<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kategori_barang', function (Blueprint $table) {
            // Tambahkan kolom checklist bertipe text
            $table->text('checklist')->nullable()->after('deskripsi');
        });
    }

    public function down()
    {
        Schema::table('kategori_barang', function (Blueprint $table) {
            $table->dropColumn('checklist');
        });
    }
};