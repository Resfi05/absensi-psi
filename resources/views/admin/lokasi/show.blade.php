@extends('layouts.admin')
@section('title', 'Detail Lokasi — ' . $lokasi->nama)
@section('page-title', 'Detail Lokasi')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-sep">›</span>
            <a href="{{ route('admin.lokasi.index') }}" class="breadcrumb-link">Lokasi Barang</a>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-current">{{ $lokasi->nama }}</span>
        </nav>
        <h2 class="page-heading">Detail <span class="heading-accent">Lokasi</span></h2>
    </div>
    <a href="{{ route('admin.lokasi.index') }}" class="btn-secondary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
        </svg>
        Kembali
    </a>
</div>

{{-- Hero Card --}}
<div class="lokasi-detail-hero">
    <div class="lokasi-detail-hero-left">
        <div class="lokasi-detail-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                <circle cx="12" cy="10" r="3"/>
            </svg>
        </div>
        <div>
            <div class="lokasi-detail-name">{{ $lokasi->nama }}</div>
            <div class="lokasi-detail-meta">
                @if($lokasi->kode)
                    <span class="lokasi-detail-kode">{{ $lokasi->kode }}</span>
                @endif
                <span>{{ $lokasi->deskripsi ?? 'Tidak ada deskripsi tambahan' }}</span>
            </div>
        </div>
    </div>
    <span class="badge-status {{ $lokasi->is_active ? 'badge-green' : 'badge-red' }}" style="font-size:0.8rem;">
        {{ $lokasi->is_active ? 'Lokasi Aktif' : 'Lokasi Nonaktif' }}
    </span>
</div>

{{-- Stats --}}
<div class="lokasi-detail-stats">
    <div class="lokasi-detail-stat" style="border-top-color:#2563eb;">
        <div class="lokasi-detail-stat-val">{{ $barang->count() }}</div>
        <div class="lokasi-detail-stat-label">Total Barang</div>
    </div>
    <div class="lokasi-detail-stat" style="border-top-color:#16a34a;">
        <div class="lokasi-detail-stat-val">{{ $total_aktif }}</div>
        <div class="lokasi-detail-stat-label">Barang Aktif</div>
    </div>
    <div class="lokasi-detail-stat" style="border-top-color:#d97706;">
        <div class="lokasi-detail-stat-val">{{ $per_kategori->count() }}</div>
        <div class="lokasi-detail-stat-label">Jenis Kategori</div>
    </div>
</div>

{{-- Ringkasan per Kategori --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <h3 class="card-title">Ringkasan per Kategori</h3>
        <span style="font-size:0.78rem;color:var(--text-muted);">Apa saja yang ada di lokasi ini</span>
    </div>
    <div class="lokasi-kategori-grid">
        @forelse($per_kategori as $pk)
        <div class="lokasi-kategori-card" style="border-left-color:{{ $pk['warna'] }};">
            <div class="lokasi-kategori-top">
                <span class="badge-jenis" style="background:{{ $pk['warna'] }}20;color:{{ $pk['warna'] }};border:1px solid {{ $pk['warna'] }}40;">
                    {{ $pk['nama'] }}
                </span>
                <div class="lokasi-kategori-dot" style="background:{{ $pk['warna'] }};"></div>
            </div>
            <div class="lokasi-kategori-val">{{ $pk['jumlah'] }}</div>
            <div class="lokasi-kategori-sub">{{ $pk['aktif'] }} aktif dari {{ $pk['jumlah'] }} unit</div>
            <div class="lokasi-kategori-bar">
                <div class="lokasi-kategori-bar-fill" style="width:{{ $pk['jumlah'] > 0 ? round($pk['aktif']/$pk['jumlah']*100) : 0 }}%;background:{{ $pk['warna'] }};"></div>
            </div>
        </div>
        @empty
        <div style="grid-column:1/-1;">
            <div class="empty-table-state" style="padding:30px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="2" y="3" width="20" height="14" rx="2"/>
                </svg>
                <p>Belum ada barang terdaftar di lokasi ini</p>
                <a href="{{ route('admin.barang.create') }}" class="btn-primary btn-sm">+ Tambah Barang</a>
            </div>
        </div>
        @endforelse
    </div>
</div>

{{-- Daftar Barang --}}
<div class="card">
    <div class="card-header">
        <div class="total-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Daftar Barang di Lokasi Ini <strong>{{ $barang->count() }}</strong>
        </div>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barang as $i => $b)
                <tr>
                    <td class="text-muted text-sm">{{ $i + 1 }}</td>
                    <td>
                        <span style="font-family:monospace;font-size:0.8rem;font-weight:700;color:#2563eb;background:#eff6ff;padding:2px 8px;border-radius:5px;">
                            {{ $b->kode_barang }}
                        </span>
                    </td>
                    <td style="font-weight:600;font-size:0.875rem;color:var(--text-main);">{{ $b->nama_barang }}</td>
                    <td>
                        @if($b->kategori)
                            <span class="badge-jenis"
                                  style="background:{{ $b->kategori->warna }}20;color:{{ $b->kategori->warna }};border:1px solid {{ $b->kategori->warna }}40;">
                                {{ $b->kategori->nama }}
                            </span>
                        @else
                            <span class="badge-jenis badge-gray">{{ $b->jenis_barang }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge-status {{ $b->is_active ? 'badge-green' : 'badge-red' }}">
                            {{ $b->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>
                        <div class="aksi-group" style="justify-content:center;">
                            <a href="{{ route('admin.barang.show', $b->id) }}" class="btn-aksi btn-aksi-view" title="Lihat Barang">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted" style="padding:30px;">Belum ada barang di lokasi ini</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('styles')
<style>
.lokasi-detail-hero {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #3b82f6 100%);
    border-radius: 16px;
    padding: 24px 28px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    position: relative;
    overflow: hidden;
}
.lokasi-detail-hero::after {
    content: '';
    position: absolute;
    right: -50px; top: -50px;
    width: 200px; height: 200px;
    background: rgba(255,255,255,0.06);
    border-radius: 50%;
}
.lokasi-detail-hero-left { display: flex; align-items: center; gap: 18px; position: relative; z-index: 1; }
.lokasi-detail-icon {
    width: 58px; height: 58px;
    background: rgba(255,255,255,0.15);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    backdrop-filter: blur(4px);
}
.lokasi-detail-icon svg { width: 28px; height: 28px; }
.lokasi-detail-name { font-size: 1.3rem; font-weight: 800; color: white; line-height: 1.3; }
.lokasi-detail-meta {
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    font-size: 0.82rem; color: rgba(255,255,255,0.85); margin-top: 6px;
}
.lokasi-detail-kode {
    font-family: monospace; font-weight: 700;
    background: rgba(255,255,255,0.18);
    padding: 2px 8px; border-radius: 5px; font-size: 0.74rem;
    color: white;
}

.lokasi-detail-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 14px;
    margin-bottom: 20px;
}
.lokasi-detail-stat {
    background: white;
    border: 1px solid var(--border);
    border-top: 3px solid;
    border-radius: 12px;
    padding: 18px 20px;
    box-shadow: var(--shadow);
}
.lokasi-detail-stat-val { font-size: 1.8rem; font-weight: 800; color: var(--text-main); line-height: 1.1; }
.lokasi-detail-stat-label { font-size: 0.78rem; color: var(--text-muted); margin-top: 4px; }

.lokasi-kategori-grid {
    padding: 18px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
    gap: 14px;
}
.lokasi-kategori-card {
    border: 1px solid var(--border);
    border-left: 4px solid;
    border-radius: 12px;
    padding: 16px 18px;
    background: #fafbfc;
}
.lokasi-kategori-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
.lokasi-kategori-dot { width: 8px; height: 8px; border-radius: 50%; }
.lokasi-kategori-val { font-size: 1.7rem; font-weight: 800; color: var(--text-main); line-height: 1; }
.lokasi-kategori-sub { font-size: 0.74rem; color: var(--text-muted); margin-top: 4px; margin-bottom: 10px; }
.lokasi-kategori-bar { height: 5px; background: #e2e8f0; border-radius: 4px; overflow: hidden; }
.lokasi-kategori-bar-fill { height: 100%; border-radius: 4px; transition: width 0.3s; }
</style>
@endpush