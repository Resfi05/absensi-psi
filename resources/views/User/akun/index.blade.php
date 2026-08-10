@extends('layouts.user')
@section('title', 'Akun Saya')
@section('content')

@if(session('success'))
<div style="background:var(--p-success-lt);color:var(--p-success);padding:12px 14px;border-radius:12px;font-size:0.82rem;margin-bottom:14px;">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background:var(--p-danger-lt);color:var(--p-danger);padding:12px 14px;border-radius:12px;font-size:0.82rem;margin-bottom:14px;">
    {{ session('error') }}
</div>
@endif

{{-- Profile Header --}}
<div class="progress-card" style="text-align:center;padding:24px 18px;">
    <div style="width:74px;height:74px;border-radius:50%;background:linear-gradient(135deg,var(--p-primary),#3B82F6);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:1.6rem;font-weight:800;color:white;overflow:hidden;position:relative;">
        @if($user->foto)
            <img src="{{ Storage::url($user->foto) }}" style="width:100%;height:100%;object-fit:cover;" id="profilePreview">
        @else
            <span id="profileInitial">{{ strtoupper(substr($user->name,0,1)) }}</span>
        @endif
        <button type="button" onclick="document.getElementById('fotoInput').click()"
                style="position:absolute;bottom:0;right:0;width:24px;height:24px;border-radius:50%;background:var(--p-primary-dk);border:2px solid white;display:flex;align-items:center;justify-content:center;cursor:pointer;">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" style="width:11px;height:11px;"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
        </button>
    </div>
    <div style="font-size:1.1rem;font-weight:800;color:var(--p-text);">{{ $user->name }}</div>
    <div style="font-size:0.78rem;color:var(--p-muted);margin-top:2px;">{{ $user->email }}</div>
    
    {{-- PERBAIKAN ERROR DI SINI --}}
    @if($user->spesialisasi && $user->spesialisasi->count() > 0)
    <span class="task-status badge-info" style="margin-top:10px;display:inline-block;">
        Spesialis {{ $user->spesialisasi->pluck('nama')->implode(', ') }}
    </span>
    @endif

    <form method="POST" action="{{ route('user.akun.profil') }}" enctype="multipart/form-data" id="fotoForm" style="display:none;">
        @csrf
        <input type="hidden" name="name" value="{{ $user->name }}">
        <input type="hidden" name="no_hp" value="{{ $user->no_hp }}">
        <input type="file" name="foto" id="fotoInput" accept="image/*" onchange="document.getElementById('fotoForm').submit()">
    </form>
</div>

{{-- Stats Ringkasan --}}
<div class="stat-grid">
    <div class="stat-card" style="text-align:center;">
        <div class="stat-val" style="color:var(--p-primary);">{{ $hariIni }}</div>
        <div class="stat-unit">Selesai Hari Ini</div>
    </div>
    <div class="stat-card" style="text-align:center;">
        <div class="stat-val">{{ $totalSelesai }}</div>
        <div class="stat-unit">Total Pengecekan</div>
    </div>
    <div class="stat-card" style="text-align:center;">
        <div class="stat-val" style="color:var(--p-success);">{{ $totalAman }}</div>
        <div class="stat-unit">Status Aman</div>
    </div>
    <div class="stat-card" style="text-align:center;">
        <div class="stat-val" style="color:var(--p-danger);">{{ $totalRusak }}</div>
        <div class="stat-unit">Perlu Tindakan</div>
    </div>
</div>

{{-- Edit Profil --}}
<div class="progress-card">
    <div class="section-label" style="margin-bottom:14px;">Edit Profil</div>
    <form method="POST" action="{{ route('user.akun.profil') }}">
        @csrf
        <div style="margin-bottom:12px;">
            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--p-text);margin-bottom:6px;">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" style="width:100%;padding:11px 14px;border-radius:11px;border:1px solid var(--p-border);font-size:0.85rem;font-family:inherit;">
        </div>
        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--p-text);margin-bottom:6px;">No. HP</label>
            <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" placeholder="08xxxxxxxxxx" style="width:100%;padding:11px 14px;border-radius:11px;border:1px solid var(--p-border);font-size:0.85rem;font-family:inherit;">
        </div>
        <button type="submit" class="btn-block btn-primary-block" style="padding:11px;">Simpan Profil</button>
    </form>
</div>

{{-- Ganti Password --}}
<div class="progress-card">
    <div class="section-label" style="margin-bottom:14px;">Ganti Password</div>
    <form method="POST" action="{{ route('user.akun.password') }}">
        @csrf
        <div style="margin-bottom:12px;">
            <input type="password" name="password_lama" placeholder="Password lama" style="width:100%;padding:11px 14px;border-radius:11px;border:1px solid var(--p-border);font-size:0.85rem;font-family:inherit;">
        </div>
        <div style="margin-bottom:12px;">
            <input type="password" name="password" placeholder="Password baru" style="width:100%;padding:11px 14px;border-radius:11px;border:1px solid var(--p-border);font-size:0.85rem;font-family:inherit;">
        </div>
        <div style="margin-bottom:14px;">
            <input type="password" name="password_confirmation" placeholder="Konfirmasi password baru" style="width:100%;padding:11px 14px;border-radius:11px;border:1px solid var(--p-border);font-size:0.85rem;font-family:inherit;">
        </div>
        <button type="submit" class="btn-block btn-outline-block" style="padding:11px;">Ubah Password</button>
    </form>
</div>

{{-- Logout --}}
<form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Yakin ingin logout?')">
    @csrf
    <button type="submit" class="btn-block btn-danger-block">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Logout
    </button>
</form>

@endsection                                                                                                                                           