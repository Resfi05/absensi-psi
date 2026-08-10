<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriBarang extends Model
{
    protected $table = 'kategori_barang';

    // REVISI: Menambahkan 'checklist' ke daftar kolom yang diizinkan (VIP)
    protected $fillable = [
        'nama', 'kode', 'ikon', 'warna', 'deskripsi', 'is_active', 'checklist'
    ];

    protected $casts = ['is_active' => 'boolean'];

    /**
     * Barang yang masuk kategori ini (via kategori_id)
     */
    public function barang()
    {
        return $this->hasMany(Barang::class, 'kategori_id');
    }

    /**
     * Petugas dengan spesialisasi kategori ini
     */
    public function petugas()
    {
        return $this->hasMany(User::class, 'spesialisasi_id');
    }

    /**
     * Warna teks kontras (hitam/putih)
     */
    public function warnaText(): string
    {
        $hex = ltrim($this->warna, '#');
        $r   = hexdec(substr($hex, 0, 2));
        $g   = hexdec(substr($hex, 2, 2));
        $b   = hexdec(substr($hex, 4, 2));
        $lum = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        return $lum > 0.5 ? '#1e293b' : '#ffffff';
    }
}