<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatBarang extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relasi ke tabel Barang
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    // Relasi ke tabel Lokasi (untuk lokasi lama)
    public function lokasiLama()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_lama_id');
    }

    // Relasi ke tabel Lokasi (untuk lokasi baru)
    public function lokasiBaru()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_baru_id');
    }
}