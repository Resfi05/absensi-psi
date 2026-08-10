<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengecekan', function (Blueprint $table) {
            $table->enum('status_tindak_lanjut', ['menunggu', 'ditangani', 'selesai', 'diabaikan'])
                  ->nullable()
                  ->after('status');
        });

        // Isi otomatis untuk data yang sudah ada: perlu_tindakan -> menunggu
        DB::table('pengecekan')
            ->where('status', 'perlu_tindakan')
            ->whereNull('status_tindak_lanjut')
            ->update(['status_tindak_lanjut' => 'menunggu']);
    }

    public function down(): void
    {
        Schema::table('pengecekan', function (Blueprint $table) {
            $table->dropColumn('status_tindak_lanjut');
        });
    }
};