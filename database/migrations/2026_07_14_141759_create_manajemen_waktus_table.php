<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('manajemen_waktu', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->integer('interval');
            $table->enum('satuan', ['hari', 'minggu', 'bulan', 'tahun']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manajemen_waktu');
    }
};