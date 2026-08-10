<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id', 'aksi', 'model_type', 'model_id',
        'deskripsi', 'data_lama', 'data_baru', 'ip_address',
    ];

    protected $casts = [
        'data_lama' => 'array',
        'data_baru' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aksiLabel(): string
    {
        return match($this->aksi) {
            'created'       => 'Menambahkan',
            'updated'       => 'Mengubah',
            'deleted'       => 'Menghapus',
            'login'         => 'Login',
            'logout'        => 'Logout',
            'tindak_lanjut' => 'Tindak Lanjut',
            'pengecekan'    => 'Pengecekan',
            default         => ucfirst($this->aksi),
        };
    }

    public function aksiColor(): string
    {
        return match($this->aksi) {
            'created'       => '#16a34a',
            'updated'       => '#2563eb',
            'deleted'       => '#ef4444',
            'login'         => '#7c3aed',
            'logout'        => '#94a3b8',
            'tindak_lanjut' => '#d97706',
            'pengecekan'    => '#0891b2',
            default         => '#64748b',
        };
    }

    public function aksiIcon(): string
    {
        return match($this->aksi) {
            'created'       => 'plus-circle',
            'updated'       => 'edit',
            'deleted'       => 'trash',
            'login'         => 'log-in',
            'logout'        => 'log-out',
            'tindak_lanjut' => 'tool',
            'pengecekan'    => 'check-circle',
            default         => 'activity',
        };
    }

    /**
     * Nama model yang mudah dibaca (Barang, Jadwal, dll)
     */
    public function modelLabel(): string
    {
        if (!$this->model_type) return '—';
        return class_basename($this->model_type);
    }
}