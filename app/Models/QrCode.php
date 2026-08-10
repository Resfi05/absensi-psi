<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    protected $table = 'qr_codes';

    protected $fillable = [
        'barang_id', 'qr_code_path', 'qr_token', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}