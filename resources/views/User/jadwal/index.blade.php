@extends('layouts.user')
@section('title', 'Jadwal')
@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
    <div>
        <h1 class="greet-title" style="font-size:1.3rem;margin-bottom:2px;">Jadwal</h1>
        <p class="greet-sub" style="margin:0;">Kelola semua jadwal aktif Anda</p>
    </div>
</div>

{{-- Tab Pills --}}
<div class="tabs-row">
    <a href="{{ route('user.jadwal.index', ['tab'=>'semua']) }}" class="tab-pill {{ $tab==='semua' ? 'active' : '' }}">Semua</a>
    <a href="{{ route('user.jadwal.index', ['tab'=>'hari_ini']) }}" class="tab-pill {{ $tab==='hari_ini' ? 'active' : '' }}">Hari Ini</a>
    <a href="{{ route('user.jadwal.index', ['tab'=>'minggu_ini']) }}" class="tab-pill {{ $tab==='minggu_ini' ? 'active' : '' }}">Minggu Ini</a>
    <a href="{{ route('user.jadwal.index', ['tab'=>'tahun_ini']) }}" class="tab-pill {{ $tab==='tahun_ini' ? 'active' : '' }}">Tahun Ini</a>
</div>

{{-- Stats Banner --}}
<div style="background:var(--p-primary-lt);border-radius:var(--p-radius);padding:18px;margin-bottom:20px;display:flex;align-items:center;gap:18px;position:relative;overflow:hidden;">
    <div style="position:relative;flex-shrink:0;">
        <div style="width:48px;height:48px;border-radius:13px;background:var(--p-surface);display:flex;align-items:center;justify-content:center;">
            <svg viewBox="0 0 24 24" fill="none" stroke="var(--p-primary)" stroke-width="2" style="width:24px;height:24px;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div style="position:absolute;bottom:-4px;right:-4px;width:20px;height:20px;border-radius:50%;background:var(--p-primary);display:flex;align-items:center;justify-content:center;border:2px solid var(--p-primary-lt);">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" style="width:10px;height:10px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
    </div>
    <div style="flex:1;">
        <div style="font-size:1.5rem;font-weight:800;color:var(--p-primary-dk);line-height:1;">{{ $jadwalAktifCount }}</div>
        <div style="font-size:0.76rem;color:var(--p-text);font-weight:600;">Jadwal Aktif</div>
        @if($terdekat)
            <div style="font-size:0.7rem;color:var(--p-muted);margin-top:2px;">
                Pengecekan berikutnya <span style="color:var(--p-primary);font-weight:700;">{{ $terdekat->sisaWaktuLabel() ?? \Carbon\Carbon::parse($terdekat->tanggal_jadwal)->translatedFormat('d M Y') }}</span>
            </div>
        @endif
    </div>
</div>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
    <span class="section-label" style="margin:0;">Daftar Jadwal</span>
    <form method="GET" action="{{ route('user.jadwal.index') }}">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <select name="sort" onchange="this.form.submit()" style="border:none;background:none;color:var(--p-primary);font-size:0.78rem;font-weight:700;font-family:inherit;">
            <option value="tanggal" {{ $sort==='tanggal' ? 'selected':'' }}> Urutkan: Tanggal</option>
            <option value="lokasi" {{ $sort==='lokasi' ? 'selected':'' }}> Urutkan: Lokasi</option>
        </select>
    </form>
</div>

@forelse($jadwalList as $j)
@php
    $tgl = \Carbon\Carbon::parse($j->tanggal_jadwal);
    $kategoriWarna = $j->barang->kategori->warna ?? '#2563EB';
    $kategoriWaktu = $j->kategoriWaktu();
    $isTerlambat = $j->isTerlambat();
    $belumWaktu = $tgl->startOfDay()->isFuture();
    $isPerbaikan = ($j->jenis_jadwal === 'perbaikan'); // 🔥 DETEKSI TUGAS PERBAIKAN

    // Tentukan warna garis tepi (border-left)
    if ($isPerbaikan) {
        $stripColor = '#ef4444'; // Merah tegas untuk Perbaikan
    } elseif ($isTerlambat) {
        $stripColor = 'var(--p-danger)';
    } elseif ($kategoriWaktu === 'hari_ini') {
        $stripColor = 'var(--p-success)';
    } elseif ($kategoriWaktu === 'besok') {
        $stripColor = 'var(--p-warning)';
    } else {
        $stripColor = 'var(--p-faint)';
    }

    if ($isTerlambat) {
        $tagLabel = 'Terlewat'; $tagClass = 'badge-rusak';
    } elseif ($kategoriWaktu === 'hari_ini') {
        $tagLabel = 'Hari Ini'; $tagClass = 'badge-aman';
    } elseif ($kategoriWaktu === 'besok') {
        $tagLabel = 'Besok'; $tagClass = 'badge-pending';
    } else {
        $tagLabel = $tgl->translatedFormat('d M Y'); $tagClass = '';
    }
@endphp

<div style="background:var(--p-surface);border-radius:var(--p-radius);box-shadow:var(--p-shadow);margin-bottom:14px;overflow:hidden;border-left:4px solid {{ $stripColor }};{{ $belumWaktu ? 'opacity:0.75;' : '' }}">
    <div style="padding:16px;">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;flex-wrap:wrap;">
            
            {{-- 🔥 POIN 12: BADGE PERBEDAAN TUGAS RUTIN VS PERBAIKAN --}}
            @if($isPerbaikan)
                <span class="task-status" style="background:#fee2e2; color:#ef4444; border:1px solid #fecaca; font-weight:800; display:inline-flex; align-items:center; gap:4px; padding:3px 8px; border-radius:6px; font-size:0.72rem;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px;"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    🚨 TUGAS PERBAIKAN
                </span>
            @else
                <span class="task-status" style="background:#e0f2fe; color:#0284c7; border:1px solid #bae6fd; font-weight:700; padding:3px 8px; border-radius:6px; font-size:0.72rem;">
                    Rutin
                </span>
            @endif

            @if($tagClass)
                <span class="task-status {{ $tagClass }}">{{ $tagLabel }}</span>
            @else
                <span style="font-size:0.7rem;color:var(--p-muted);font-weight:600;">{{ $tagLabel }}</span>
            @endif
            
            @if($j->jamLabel())
                <span style="font-size:0.76rem;font-weight:700;color:{{ $isTerlambat ? 'var(--p-danger)' : 'var(--p-text)' }};">{{ $j->jamLabel() }}</span>
            @endif
            
            @if($belumWaktu)
                <span style="margin-left:auto;display:flex;align-items:center;gap:3px;font-size:0.68rem;color:var(--p-faint);font-weight:600;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:11px;height:11px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    Terkunci
                </span>
            @endif
        </div>

        @if($belumWaktu)
        <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:12px;cursor:not-allowed;">
        @else
        <a href="{{ route('user.jadwal.show', $j->id) }}" style="display:flex;align-items:flex-start;gap:12px;margin-bottom:12px;text-decoration:none;color:inherit;">
        @endif

            {{-- 🔥 POIN 10: TAMPILKAN FOTO BARANG SEBAGAI IKON KARTU JIKA ADA --}}
            @if($j->barang->foto)
                <img src="{{ Storage::url($j->barang->foto) }}" alt="{{ $j->barang->nama_barang }}" 
                     style="width:42px; height:42px; min-width:42px; border-radius:10px; object-fit:cover; border:1px solid var(--p-border); flex-shrink:0;">
            @else
                <div class="task-icon" style="background:{{ $kategoriWarna }}18;color:{{ $kategoriWarna }};flex-shrink:0;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                </div>
            @endif

            <div style="flex:1;min-width:0;">
                <div class="task-name" style="margin-bottom:4px;color:var(--p-text);font-weight:700;">{{ $j->barang->nama_barang }}</div>
                <div class="task-loc" style="color:var(--p-muted);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/></svg>
                    {{ $j->barang->lokasiRelasi->nama ?? 'Lokasi belum diatur' }}
                </div>
            </div>
            @if(!$belumWaktu)
                <svg viewBox="0 0 24 24" fill="none" stroke="var(--p-faint)" stroke-width="2" style="width:16px;height:16px;flex-shrink:0;margin-top:4px;"><polyline points="9 18 15 12 9 6"/></svg>
            @endif
        @if($belumWaktu)
        </div>
        @else
        </a>
        @endif

        {{-- 🔥 POIN 12: PESAN PERINGATAN KHUSUS TUGAS PERBAIKAN --}}
        @if($isPerbaikan)
        <div style="display:flex;align-items:center;gap:8px;background:#fff1f2;color:#e11d48;padding:8px 12px;border-radius:10px;font-size:0.76rem;font-weight:700;margin-bottom:12px;border:1px dashed #fecdd3;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;flex-shrink:0;"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Tindak Lanjut: Harap lakukan perbaikan & upload foto sesudah perbaikan!
        </div>
        @elseif($j->status === 'pending' && $isTerlambat)
        <div style="display:flex;align-items:center;gap:6px;background:var(--p-danger-lt);color:var(--p-danger);padding:8px 12px;border-radius:10px;font-size:0.76rem;font-weight:600;margin-bottom:12px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Jadwal belum dikerjakan
        </div>
        @endif

        <div style="display:flex;gap:10px;">
            @if($belumWaktu)
                <span class="btn-block btn-outline-block" style="padding:11px;font-size:0.8rem;opacity:0.5;cursor:not-allowed;">Detail</span>
                <span class="btn-block" style="padding:11px;font-size:0.8rem;background:var(--p-bg);color:var(--p-faint);cursor:not-allowed;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    Terkunci
                </span>
            @elseif($isTerlambat || $isPerbaikan)
                <a href="{{ route('user.jadwal.show', $j->id) }}" class="btn-block btn-outline-block" style="padding:11px;font-size:0.8rem;">Detail</a>
                <a href="{{ route('user.scan', ['jadwal'=>$j->id]) }}" class="btn-block" style="padding:11px;font-size:0.8rem;background:#ef4444;color:white;font-weight:700;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><path d="M3 7V5a2 2 0 012-2h2M17 3h2a2 2 0 012 2v2M21 17v2a2 2 0 01-2 2h-2M7 21H5a2 2 0 01-2-2v-2"/><rect x="7" y="7" width="10" height="10" rx="1"/></svg>
                    {{ $isPerbaikan ? 'Perbaiki Sekarang' : 'Kerjakan Sekarang' }}
                </a>
            @else
                <a href="{{ route('user.jadwal.show', $j->id) }}" class="btn-block btn-outline-block" style="padding:11px;font-size:0.8rem;">Detail</a>
                <a href="{{ route('user.scan', ['jadwal'=>$j->id]) }}" class="btn-block btn-primary-block" style="padding:11px;font-size:0.8rem;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;"><path d="M3 7V5a2 2 0 012-2h2M17 3h2a2 2 0 012 2v2M21 17v2a2 2 0 01-2 2h-2M7 21H5a2 2 0 01-2-2v-2"/><rect x="7" y="7" width="10" height="10" rx="1"/></svg>
                    Scan & Mulai
                </a>
            @endif
        </div>
    </div>
</div>
@empty
<div class="empty-state">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
    </svg>
    <p>Hore! Tidak ada tugas aktif di tab ini.</p>
</div>
@endforelse

@endsection