<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // 🔥 REVISI: Hapus 'spesialisasi_id' dari sini
    protected $fillable = [
        'name', 'username', 'email', 'password',
        'role', 'no_hp', 'foto',
        'email_notification_preference', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password'                      => 'hashed',
        'is_active'                     => 'boolean',
        'email_notification_preference' => 'boolean',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    // 🔥 REVISI: Relasi menjadi Many-to-Many
    public function spesialisasi()
    {
        return $this->belongsToMany(KategoriBarang::class, 'kategori_barang_user', 'user_id', 'kategori_barang_id')->withTimestamps();
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function pengecekan()
    {
        return $this->hasMany(Pengecekan::class);
    }
}