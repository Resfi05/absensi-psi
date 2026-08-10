@extends('layouts.admin')
@section('title', 'Detail Pengecekan')
@section('page-title', 'Detail Pengecekan')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-sep">›</span>
            <a href="{{ route('admin.monitoring.index') }}" class="breadcrumb-link">Monitoring Pengecekan</a>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-current">Detail</span>
        </nav>
        <h2 class="page-heading">Detail <span class="heading-accent">Pengecekan</span></h2>
    </div>
    <a href="{{ route('admin.monitoring.index') }}" class="btn-secondary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
        </svg>
        Kembali
    </a>
</div>

{{-- Status Hero --}}
<div class="mon-detail-hero" style="background:{{ $pengecekan->statusColor() }}10;border:1px solid {{ $pengecekan->statusColor() }}30;">
    <div class="mon-detail-hero-icon" style="background:{{ $pengecekan->statusColor() }}20;color:{{ $pengecekan->statusColor() }};">
        @if($pengecekan->status === 'aman')
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
        @elseif($pengecekan->status === 'perlu_tindakan')
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        @else
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        @endif
    </div>
    <div>
        <div style="font-size:1.1rem;font-weight:800;color:{{ $pengecekan->statusColor() }};">{{ $pengecekan->statusLabel() }}</div>
        <div style="font-size:0.82rem;color:var(--text-muted);margin-top:2px;">
            {{ $pengecekan->jadwal->barang->nama_barang ?? '—' }}
            @if($pengecekan->checked_at)
                — Diperiksa {{ \Carbon\Carbon::parse($pengecekan->checked_at)->translatedFormat('d F Y, H:i') }}
            @endif
        </div>
    </div>
</div>

<div class="show-grid">
    <div class="show-col-main">

        {{-- Checklist --}}
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                </svg>
                Checklist Pengecekan
            </div>
            <div style="padding:18px;">
                @if($pengecekan->checklist_data)
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        @foreach($pengecekan->checklist_data as $item)
                        <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:{{ $item['checked'] ? '#f0fdf4' : '#fef2f2' }};border-radius:8px;border:1px solid {{ $item['checked'] ? '#bbf7d0' : '#fecaca' }};">
                            <div style="width:22px;height:22px;border-radius:50%;background:{{ $item['checked'] ? '#16a34a' : '#ef4444' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                @if($item['checked'])
                                    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" style="width:12px;height:12px;"><polyline points="20 6 9 17 4 12"/></svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" style="width:10px;height:10px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                @endif
                            </div>
                            <span style="font-size:0.85rem;color:var(--text-main);font-weight:500;">{{ $item['item'] }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align:center;padding:30px 0;color:var(--text-muted);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:40px;height:40px;opacity:0.3;margin-bottom:8px;">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <p style="font-size:0.85rem;">Checklist belum diisi — petugas belum menyelesaikan pengecekan ini.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Foto --}}
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
                Bukti Foto Lapangan
            </div>
            <div style="padding:18px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <div style="font-size:0.76rem;font-weight:600;color:var(--text-muted);margin-bottom:8px;">
                        Kondisi Barang Saat Ditemukan
                    </div>
                    @if($pengecekan->photo_before)
                        <img src="{{ asset('storage/' . $pengecekan->photo_before) }}" style="width:100%;height:180px;object-fit:cover;border-radius:10px;border:1px solid var(--border);">
                    @else
                        <div style="width:100%;height:180px;background:#f1f5f9;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#cbd5e1;border:1px dashed var(--border);">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:32px;height:32px;"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
                        </div>
                    @endif
                </div>
                <div>
                    <div style="font-size:0.76rem;font-weight:600;color:var(--text-muted);margin-bottom:8px;">
                        Keadaan Tindak Lanjut
                    </div>
                    @if($pengecekan->photo_after)
                        <img src="{{ asset('storage/' . $pengecekan->photo_after) }}" style="width:100%;height:180px;object-fit:cover;border-radius:10px;border:1px solid var(--border);">
                    @else
                        <div style="width:100%;height:180px;background:#f1f5f9;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#cbd5e1;border:1px dashed var(--border);">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:32px;height:32px;"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Catatan --}}
        @if($pengecekan->notes)
        <div class="form-card" style="border-color:#fecaca;">
            <div class="form-card-header" style="background:#fff5f5;color:#ef4444;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                Catatan Temuan dari Petugas
            </div>
            <div style="padding:18px;">
                <p style="font-size:0.875rem;color:var(--text-main);line-height:1.6;">{{ $pengecekan->notes }}</p>
            </div>
        </div>
        @endif

    </div>

    <div class="show-col-side">

        {{-- Info Petugas --}}
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
                Petugas
            </div>
            <div class="form-card-body" style="align-items:center;text-align:center;gap:8px;">
                <div style="width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#1e40af,#3b82f6);color:white;display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:700;">
                    {{ strtoupper(substr($pengecekan->user->name ?? '?',0,1)) }}
                </div>
                <div style="font-weight:700;color:var(--text-main);">{{ $pengecekan->user->name ?? '—' }}</div>
                <div style="font-size:0.78rem;color:var(--text-muted);">{{ $pengecekan->user->no_hp ?? '' }}</div>
                @if($pengecekan->user && $pengecekan->user->spesialisasi && $pengecekan->user->spesialisasi->count() > 0)
    @foreach($pengecekan->user->spesialisasi as $sp)
        <span class="badge-jenis" style="background:{{ $sp->warna }}20;color:{{ $sp->warna }};border:1px solid {{ $sp->warna }}40;">
            Spesialis {{ $sp->nama }}
        </span>
    @endforeach
@endif
            </div>
        </div>

        {{-- Info Barang --}}
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="3" width="20" height="14" rx="2"/>
                    <line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
                </svg>
                Info Barang
            </div>
            <div class="form-card-body" style="gap:10px;">
                <div style="font-size:0.75rem;color:var(--text-muted);">Kode Barang</div>
                <div style="font-family:monospace;font-size:0.875rem;font-weight:700;color:#2563eb;">{{ $pengecekan->jadwal->barang->kode_barang ?? '—' }}</div>
                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px;">Nama Barang</div>
                <div style="font-size:0.875rem;font-weight:600;">{{ $pengecekan->jadwal->barang->nama_barang ?? '—' }}</div>
                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px;">Lokasi</div>
                <div style="font-size:0.875rem;font-weight:600;">
                    📍 {{ $pengecekan->jadwal->barang->lokasiRelasi->nama ?? '—' }}
                </div>
                @if($pengecekan->jadwal->barang->id ?? null)
                <div style="margin-top:8px;">
                    <a href="{{ route('admin.barang.show', $pengecekan->jadwal->barang->id) }}" class="btn-secondary btn-sm btn-full">
                        Lihat Detail Barang
                    </a>
                </div>
                @endif
            </div>
        </div>

        @if($pengecekan->status === 'perlu_tindakan')
        <div class="form-card" style="border-color:#fecaca;">
            <div class="form-card-body" style="gap:10px;">
                <div style="background:#fef2f2;border-radius:8px;padding:10px 12px;font-size:0.78rem;color:#ef4444;text-align:center;">
                    ⚠️ Temuan ini butuh tindak lanjut.
                </div>
                @if($pengecekan->status_tindak_lanjut)
                <div style="text-align:center;font-size:0.76rem;color:var(--text-muted);">
                    Status penanganan saat ini:
                    <span class="badge-status" style="background:{{ $pengecekan->statusTindakLanjutColor() }}20;color:{{ $pengecekan->statusTindakLanjutColor() }};border:1px solid {{ $pengecekan->statusTindakLanjutColor() }}40;margin-left:4px;">
                        {{ $pengecekan->statusTindakLanjutLabel() }}
                    </span>
                </div>
                @endif
                <a href="{{ route('admin.review.show', $pengecekan->id) }}" class="btn-primary btn-full" style="background:#ef4444;box-shadow:0 2px 8px rgba(239,68,68,0.3);justify-content:center;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    Lihat & Tindak Lanjuti di Review Hasil
                </a>
            </div>
        </div>
        @endif

    </div>
</div>

@endsection

@push('styles')
<style>
.mon-detail-hero {
    border-radius: 14px;
    padding: 20px 24px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
}
.mon-detail-hero-icon {
    width: 50px; height: 50px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.mon-detail-hero-icon svg { width: 24px; height: 24px; }
</style>
@endpush