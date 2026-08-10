@extends('layouts.user')
@section('title', 'Riwayat')
@section('content')

<h1 class="greet-title" style="font-size:1.25rem;">Riwayat <span class="accent">Pengecekan</span></h1>
<p class="greet-sub">Semua tugas yang sudah Anda kerjakan</p>

<form method="GET" action="{{ route('user.riwayat') }}" style="margin-bottom:14px;">
    <div style="position:relative;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:var(--p-faint);">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang..."
               style="width:100%;padding:12px 14px 12px 40px;border-radius:13px;border:1px solid var(--p-border);background:var(--p-surface);font-size:0.85rem;font-family:inherit;">
    </div>
</form>

@forelse($riwayat as $r)
@php
    // Ambil warna asli kategori barang, default ke biru kalau kosong
    $kategoriWarna = $r->jadwal->barang->kategori->warna ?? '#2563EB';
@endphp
<a href="{{ route('user.pengecekan.show', $r->id) }}" class="task-card">
    <div class="task-card-top" style="align-items:flex-start;">
        
        {{-- 🔥 REVISI POIN 10: TAMPILKAN FOTO BARANG ATAU IKON KUBUS 🔥 --}}
        @if($r->jadwal->barang->foto)
            <img src="{{ Storage::url($r->jadwal->barang->foto) }}" alt="{{ $r->jadwal->barang->nama_barang }}" 
                 style="width:42px; height:42px; min-width:42px; border-radius:10px; object-fit:cover; border:1px solid var(--p-border); flex-shrink:0;">
        @else
            <div class="task-icon" style="background:{{ $kategoriWarna }}20;color:{{ $kategoriWarna }};flex-shrink:0;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
            </div>
        @endif

        <div class="task-info" style="margin-left:10px; padding-top:2px;">
            <div class="task-name">{{ $r->jadwal->barang->nama_barang }}</div>
            <div class="task-loc">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/></svg>
                {{ $r->jadwal->barang->lokasiRelasi->nama ?? '???' }}
            </div>
        </div>
        <span class="task-status {{ $r->status === 'aman' ? 'badge-aman' : 'badge-rusak' }}" style="margin-top:2px;">
            {{ $r->status === 'aman' ? 'Aman' : 'Perlu Tindakan' }}
        </span>
    </div>
    <div class="task-card-footer">
        <span class="task-date">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            {{ $r->checked_at->translatedFormat('d M Y, H:i') }}
        </span>
        <span class="task-btn">
            Detail
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </span>
    </div>
</a>
@empty
<div class="empty-state">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
    </svg>
    <p>Belum ada riwayat pengecekan</p>
</div>
@endforelse

@if($riwayat->hasPages())
<div style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;background:var(--p-surface);padding:12px 16px;border-radius:var(--p-radius);box-shadow:var(--p-shadow);">
    @if($riwayat->onFirstPage())
        <span style="color:var(--p-muted);font-size:0.85rem;font-weight:600;display:flex;align-items:center;gap:4px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><polyline points="15 18 9 12 15 6"/></svg> Sebelumnya
        </span>
    @else
        <a href="{{ $riwayat->previousPageUrl() }}" style="color:var(--p-primary);font-size:0.85rem;font-weight:700;display:flex;align-items:center;gap:4px;text-decoration:none;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><polyline points="15 18 9 12 15 6"/></svg> Sebelumnya
        </a>
    @endif

    @if($riwayat->hasMorePages())
        <a href="{{ $riwayat->nextPageUrl() }}" style="color:var(--p-primary);font-size:0.85rem;font-weight:700;display:flex;align-items:center;gap:4px;text-decoration:none;">
            Selanjutnya <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
    @else
        <span style="color:var(--p-muted);font-size:0.85rem;font-weight:600;display:flex;align-items:center;gap:4px;">
            Selanjutnya <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><polyline points="9 18 15 12 9 6"/></svg>
        </span>
    @endif
</div>
@endif

@endsection