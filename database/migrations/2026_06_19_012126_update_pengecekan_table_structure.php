<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek dulu apakah tabel pengecekan sudah ada (dari migration awal project)
        if (!Schema::hasTable('pengecekan')) {
            Schema::create('pengecekan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('jadwal_id')->constrained('jadwal')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->json('checklist_data')->nullable();
                $table->string('photo_before')->nullable();
                $table->string('photo_after')->nullable();
                $table->text('notes')->nullable();
                $table->enum('status', ['aman', 'perlu_tindakan', 'tertunda'])->default('tertunda');
                $table->timestamp('checked_at')->nullable();
                $table->timestamps();
            });
        } else {
            // Tabel sudah ada dari migration lama, tambahkan kolom yang belum ada
            Schema::table('pengecekan', function (Blueprint $table) {
                if (!Schema::hasColumn('pengecekan', 'checklist_data')) {
                    $table->json('checklist_data')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('pengecekan', 'photo_before')) {
                    $table->string('photo_before')->nullable();
                }
                if (!Schema::hasColumn('pengecekan', 'photo_after')) {
                    $table->string('photo_after')->nullable();
                }
                if (!Schema::hasColumn('pengecekan', 'notes')) {
                    $table->text('notes')->nullable();
                }
                if (!Schema::hasColumn('pengecekan', 'checked_at')) {
                    $table->timestamp('checked_at')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('pengecekan', function (Blueprint $table) {
            $table->dropColumn(['checklist_data', 'photo_before', 'photo_after', 'notes', 'checked_at']);
        });
    }
};