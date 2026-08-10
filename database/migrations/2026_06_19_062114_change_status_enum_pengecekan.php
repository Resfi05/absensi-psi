<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE pengecekan MODIFY COLUMN status ENUM('aman','perlu_tindakan','tertunda') NOT NULL DEFAULT 'tertunda'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pengecekan MODIFY COLUMN status ENUM('proses','selesai') NOT NULL DEFAULT 'proses'");
    }
};