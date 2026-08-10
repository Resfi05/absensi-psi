@extends('layouts.user')
@section('title', 'Scan Gagal')
@section('content')

<div style="text-align:center;padding:50px 20px;">
    @if($belumWaktu ?? false)
        <div style="width:84px;height:84px;border-radius:50%;background:var(--p-warning-lt);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="var(--p-warning)" stroke-width="2" style="width:40px;height:40px;">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
            </svg>
        </div>
        <h2 style="font-size:1.1rem;font-weight:800;color:var(--p-text);margin-bottom:8px;">Belum Waktunya</h2>
    @else
        <div style="width:84px;height:84px;border-radius:50%;background:var(--p-danger-lt);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="var(--p-danger)" stroke-width="2" style="width:40px;height:40px;">
                <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
        </div>
        <h2 style="font-size:1.1rem;font-weight:800;color:var(--p-text);margin-bottom:8px;">Scan Tidak Berhasil</h2>
    @endif

    <p style="font-size:0.85rem;color:var(--p-muted);line-height:1.6;max-width:300px;margin:0 auto 28px;">
        {{ $pesan ?? 'Tugas ini tidak dapat diakses atau dibuka pada saat ini.' }}
    </p>

    @if(($belumWaktu ?? false) && isset($jadwal))
        <a href="{{ route('user.jadwal.show', $jadwal->id) }}" class="btn-block btn-primary-block" style="margin-bottom:10px;">
            Lihat Detail Tugas
        </a>
        <a href="{{ route('user.jadwal.index') }}" class="btn-block btn-outline-block">
            Kembali ke Jadwal
        </a>
    @else
        <a href="{{ route('user.scan') }}" class="btn-block btn-primary-block" style="margin-bottom:10px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 7V5a2 2 0 012-2h2M17 3h2a2 2 0 012 2v2M21 17v2a2 2 0 01-2 2h-2M7 21H5a2 2 0 01-2-2v-2"/><rect x="7" y="7" width="10" height="10" rx="1"/>
            </svg>
            Coba Scan Lagi
        </a>
        <a href="{{ route('user.dashboard') }}" class="btn-block btn-outline-block">
            Kembali ke Dashboard
        </a>
    @endif
</div>

@endsection