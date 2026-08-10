<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id', 'judul', 'pesan', 'tipe', 'link', 'link_id', 'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper: buat notifikasi untuk SATU user
     */
    public static function kirim(int $userId, string $judul, string $pesan, string $tipe = 'info', ?string $link = null, ?int $linkId = null): self
    {
        return self::create([
            'user_id'  => $userId,
            'judul'    => $judul,
            'pesan'    => $pesan,
            'tipe'     => $tipe,
            'link'     => $link,
            'link_id'  => $linkId,
            'is_read'  => false,
        ]);
    }

    /**
     * Helper: kirim ke SEMUA petugas dengan spesialisasi tertentu
     * (dipakai untuk notifikasi jadwal baru — skill-based, siapa cepat dia dapat)
     */
    public static function kirimKeSpesialisasi(int $kategoriId, string $judul, string $pesan, string $tipe = 'jadwal_baru', ?string $link = null, ?int $linkId = null): void
    {
        $petugasList = User::where('role', 'user')
            ->where('is_active', true)
            ->where('spesialisasi_id', $kategoriId)
            ->pluck('id');

        foreach ($petugasList as $userId) {
            self::kirim($userId, $judul, $pesan, $tipe, $link, $linkId);
        }
    }
}