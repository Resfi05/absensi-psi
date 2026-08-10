<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengecekan extends Model
{
    protected $table = 'pengecekan';

    protected $fillable = [
        'jadwal_id', 'user_id', 'checklist_data',
        'photo_before', 'photo_after', 'notes',
        'status', 'status_tindak_lanjut', 'checked_at',
    ];

    protected $casts = [
        'checklist_data' => 'array',
        'checked_at'     => 'datetime',
    ];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barang()
    {
        return $this->hasOneThrough(
            Barang::class,
            Jadwal::class,
            'id', 'id', 'jadwal_id', 'barang_id'
        );
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'aman'           => 'Aman',
            'perlu_tindakan' => 'Perlu Tindakan',
            'tertunda'       => 'Tertunda',
            default          => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'aman'           => '#16a34a',
            'perlu_tindakan' => '#ef4444',
            'tertunda'       => '#d97706',
            default          => '#64748b',
        };
    }

    public function statusTindakLanjutLabel(): string
    {
        return match($this->status_tindak_lanjut) {
            'menunggu'  => 'Menunggu Review',
            'ditangani' => 'Sedang Ditangani',
            'selesai'   => 'Selesai Diperbaiki',
            'diabaikan' => 'Diabaikan',
            'terlambat' => 'Terlambat / Overdue', // 🔥 STATUS BARU
            default     => '???',
        };
    }

    public function statusTindakLanjutColor(): string
    {
        return match($this->status_tindak_lanjut) {
            'menunggu'  => '#d97706', // Oranye
            'ditangani' => '#2563eb', // Biru
            'selesai'   => '#16a34a', // Hijau
            'diabaikan' => '#94a3b8', // Abu-abu
            'terlambat' => '#ef4444', // 🔥 Merah Peringatan (Danger)
            default     => '#64748b',
        };
    }
}