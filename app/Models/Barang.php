<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';

    protected $fillable = [
        'kode_barang', 'nama_barang', 'jenis_barang',
        'merk', 'model', 'tipe',
        'kategori_id', 'lokasi_id',
        'lokasi', 'gedung', 'lantai', 'ruangan',
        'keterangan', 'foto', 'is_active', 'tanggal_expired',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tanggal_expired' => 'date',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriBarang::class, 'kategori_id');
    }

    public function lokasiRelasi()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    public function qrCode()
    {
        return $this->hasOne(QrCode::class);
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function pengecekan()
    {
        return $this->hasMany(Pengecekan::class);
    }

    public function getNamaLokasiAttribute(): string
    {
        return $this->lokasiRelasi?->nama ?? $this->lokasi ?? '—';
    }
}