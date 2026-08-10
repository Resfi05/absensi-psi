<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengecekan', function (Blueprint $table) {
            $table->foreignId('barang_id')->nullable()->change();
            $table->foreignId('qr_code_id')->nullable()->change();
            $table->timestamp('tanggal_pengecekan')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pengecekan', function (Blueprint $table) {
            $table->foreignId('barang_id')->nullable(false)->change();
            $table->foreignId('qr_code_id')->nullable(false)->change();
            $table->timestamp('tanggal_pengecekan')->nullable(false)->change();
        });
    }
};