<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Notifikasi eksternal (email) — boleh di-toggle oleh user
            // Notifikasi internal (badge sidebar) TIDAK disimpan di sini, karena wajib & tidak bisa dimatikan
            $table->boolean('email_notification_preference')->default(true)->after('foto');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email_notification_preference');
        });
    }
};