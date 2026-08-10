<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal', function (Blueprint $table) {
            $table->string('jenis_jadwal')->default('rutin')->after('keterangan');
            $table->unsignedBigInteger('parent_pengecekan_id')->nullable()->after('jenis_jadwal');

            // Tambahkan foreign key agar relasinya kuat (Opsional tapi sangat disarankan)
            $table->foreign('parent_pengecekan_id')->references('id')->on('pengecekan')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropForeign(['parent_pengecekan_id']);
            $table->dropColumn(['jenis_jadwal', 'parent_pengecekan_id']);
        });
    }
};