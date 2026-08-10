<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Jadwal extends Model
{
    protected $table = 'jadwal';

    protected $fillable = [
        'judul', 'user_id', 'barang_id',
        'tanggal_mulai', 'tanggal_jadwal',
        'jam_mulai', 'jam_selesai',
        'frekuensi', 'status', 'keterangan',
        'jenis_jadwal', 'parent_pengecekan_id', 
    ];

    protected $casts = [
        'tanggal_jadwal' => 'date',
        'tanggal_mulai'  => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function pengecekan()
    {
        return $this->hasMany(Pengecekan::class);
    }

    public function parentPengecekan()
    {
        return $this->belongsTo(Pengecekan::class, 'parent_pengecekan_id');
    }

    public function tanggalBerikutnya(): Carbon
    {
        $base = Carbon::parse($this->tanggal_jadwal);
        $masterWaktu = \App\Models\ManajemenWaktu::where('nama', $this->frekuensi)->first();
        
        if ($masterWaktu) {
            return match($masterWaktu->satuan) {
                'hari'   => $base->addDays($masterWaktu->interval),
                'minggu' => $base->addWeeks($masterWaktu->interval),
                'bulan'  => $base->addMonths($masterWaktu->interval),
                'tahun'  => $base->addYears($masterWaktu->interval),
                default  => $base->addMonth(),
            };
        }

        return match($this->frekuensi) {
            'harian'   => $base->addDay(),
            'mingguan' => $base->addWeek(),
            'bulanan'  => $base->addMonth(),
            default    => $base->addMonth(),
        };
    }

    public function frekuensiLabel(): string
    {
        return ucfirst(str_replace('_', ' ', $this->frekuensi));
    }

    public function isTerlambat(): bool
    {
        return $this->status === 'pending'
            && Carbon::parse($this->tanggal_jadwal)->isPast();
    }

    public function isJatuhTempo(int $days = 7): bool
    {
        $tgl = Carbon::parse($this->tanggal_jadwal);
        return $this->status === 'pending'
            && !$tgl->isPast()
            && $tgl->diffInDays(now()) <= $days;
    }

    public function scopeUntukPetugas($query, User $user)
    {
        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->spesialisasi_id) {
            $query->whereHas('barang', function ($q) use ($user) {
                $q->where('kategori_id', $user->spesialisasi_id);
            });
        }

        return $query;
    }

    public function jamLabel(): ?string
    {
        if (!$this->jam_mulai) return null;

        $mulai = Carbon::parse($this->jam_mulai)->format('H:i');
        $selesai = $this->jam_selesai ? Carbon::parse($this->jam_selesai)->format('H:i') : null;

        return $selesai ? "{$mulai} - {$selesai}" : $mulai;
    }

    public function kategoriWaktu(): string
    {
        $tgl = Carbon::parse($this->tanggal_jadwal);

        if ($tgl->isToday()) return 'hari_ini';
        if ($tgl->isTomorrow()) return 'besok';
        if ($tgl->isSameWeek(now()) && $tgl->isFuture()) return 'minggu_ini';

        return 'lainnya';
    }

    public function sisaWaktuLabel(): ?string
    {
        if (!$this->jam_mulai || $this->kategoriWaktu() !== 'hari_ini') return null;

        $targetDateTime = Carbon::parse($this->tanggal_jadwal->format('Y-m-d') . ' ' . $this->jam_mulai);

        if ($targetDateTime->isPast()) return null;

        $diffMinutes = now()->diffInMinutes($targetDateTime);

        if ($diffMinutes < 60) {
            return "{$diffMinutes} menit";
        }

        $hours = floor($diffMinutes / 60);
        return "{$hours} jam";
    }

    public function buatJadwalBerikutnya(): ?self
    {
        if (!$this->barang->lokasi_id || !$this->barang->kategori_id) {
            return null;
        }

        if ($this->jenis_jadwal === 'perbaikan') {
            return null; 
        }

        $tglBerikutnya = $this->tanggalBerikutnya();

        $sudahAda = self::where('barang_id', $this->barang_id)
            ->where('status', 'pending')
            ->where('jenis_jadwal', '!=', 'perbaikan') 
            ->whereDate('tanggal_jadwal', $tglBerikutnya->format('Y-m-d'))
            ->exists();

        if ($sudahAda) {
            return null;
        }

        $jadwalBaru = self::create([
            'judul'          => 'Pengecekan ' . $this->barang->nama_barang,
            'barang_id'      => $this->barang_id,
            'user_id'        => null,
            'tanggal_mulai'  => $this->tanggal_mulai ?? $this->tanggal_jadwal,
            'tanggal_jadwal' => $tglBerikutnya,
            'jam_mulai'      => $this->jam_mulai,
            'jam_selesai'    => $this->jam_selesai,
            'frekuensi'      => $this->frekuensi,
            'status'         => 'pending',
            'keterangan'     => $this->keterangan,
            'jenis_jadwal'   => 'rutin',
        ]);

        if ($jadwalBaru) {
            \App\Models\Notification::kirimKeSpesialisasi(
                kategoriId: $this->barang->kategori_id,
                judul: 'Jadwal Baru: ' . $this->barang->nama_barang,
                pesan: 'Tugas pengecekan ' . $this->barang->nama_barang . ' di ' .
                       ($this->barang->lokasiRelasi->nama ?? 'lokasi belum diatur') .
                       ' dijadwalkan pada ' . $tglBerikutnya->translatedFormat('d F Y') . '.',
                tipe: 'jadwal_baru',
                link: 'user.jadwal.show',
                linkId: $jadwalBaru->id,
            );
        }

        return $jadwalBaru;
    }
}