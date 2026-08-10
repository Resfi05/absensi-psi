<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('barang', function (Blueprint $table) {
        $table->string('merk')->nullable()->after('nama_barang');
        $table->string('model')->nullable()->after('merk');
        $table->string('tipe')->nullable()->after('model');
    });
}

public function down()
{
    Schema::table('barang', function (Blueprint $table) {
        $table->dropColumn(['merk', 'model', 'tipe']);
    });
}
};
