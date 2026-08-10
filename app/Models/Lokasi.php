<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    protected $table = 'lokasi';

    protected $fillable = [
        'nama', 'kode', 'deskripsi', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function barang()
    {
        return $this->hasMany(Barang::class, 'lokasi_id');
    }
}