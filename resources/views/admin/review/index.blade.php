@extends('layouts.admin')
@section('title', 'Review Hasil')
@section('page-title', 'Review Hasil')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-current">Review Hasil</span>
        </nav>
        <h2 class="page-heading">Review <span class="heading-accent">Hasil Pengecekan</span></h2>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" id="flashAlert">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    {{ session('success') }}
    <button class="alert-close" onclick="this.parentElement.remove()">✕</button>
</div>
@endif

{{-- Info Banner --}}
<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px;margin-bottom:20px;display:flex;gap:12px;align-items:flex-start;">
    <svg viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" style="width:20px;height:20px;flex-shrink:0;margin-top:1px;">
        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
    <div style="font-size:0.8rem;color:#991b1b;">
        Halaman ini khusus menampilkan temuan yang <strong>butuh tindak lanjut</strong>. Barang dengan kondisi aman tidak ditampilkan di sini — cek di
        <a href="{{ route('admin.monitoring.index') }}" style="font-weight:700;text-decoration:underline;">Monitoring Pengecekan</a>.
    </div>
</div>

{{-- Stats Cards — Metrik Penanganan Masalah --}}
<div class="rev-stats-row">
    <div class="rev-stat-card" style="border-top-color:#ef4444;">
        <div class="rev-stat-icon" style="background:#fee2e2;color:#ef4444;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
        </div>
        <div>
            <div class="rev-stat-val">{{ $totalTemuan }}</div>
            <div class="rev-stat-label">Total Temuan Masalah</div>
        </div>
    </div>
    <div class="rev-stat-card" style="border-top-color:#d97706;">
        <div class="rev-stat-icon" style="background:#fef3c7;color:#d97706;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <div>
            <div class="rev-stat-val">{{ $menungguReview }}</div>
            <div class="rev-stat-label">Menunggu Review</div>
        </div>
    </div>
    <div class="rev-stat-card" style="border-top-color:#2563eb;">
        <div class="rev-stat-icon" style="background:#dbeafe;color:#2563eb;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
            </svg>
        </div>
        <div>
            <div class="rev-stat-val">{{ $sedangDitangani }}</div>
            <div class="rev-stat-label">Sedang Ditangani</div>
        </div>
    </div>
    <div class="rev-stat-card" style="border-top-color:#16a34a;">
        <div class="rev-stat-icon" style="background:#dcfce7;color:#16a34a;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>
        <div>
            <div class="rev-stat-val">{{ $selesaiDiperbaiki }}</div>
            <div class="rev-stat-label">Selesai Diperbaiki</div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="card" style="margin-bottom:16px;">
    <form method="GET" action="{{ route('admin.review.index') }}" class="jadwal-filter-form">
        <select name="kategori" class="filter-select" onchange="this.form.submit()">
            <option value="semua" {{ !request('kategori') || request('kategori')==='semua' ? 'selected':'' }}>Semua Jenis</option>
            @foreach($kategori_list as $k)
                <option value="{{ $k->id }}" {{ request('kategori') == $k->id ? 'selected':'' }}>{{ $k->nama }}</option>
            @endforeach
        </select>

        <select name="lokasi" class="filter-select" onchange="this.form.submit()">
            <option value="semua" {{ !request('lokasi') || request('lokasi')==='semua' ? 'selected':'' }}>Semua Lokasi</option>
            @foreach($lokasi_list as $lok)
                <option value="{{ $lok->id }}" {{ request('lokasi') == $lok->id ? 'selected':'' }}>{{ $lok->nama }}</option>
            @endforeach
        </select>

        <select name="status_tindak_lanjut" class="filter-select" onchange="this.form.submit()">
            <option value="semua" {{ !request('status_tindak_lanjut') || request('status_tindak_lanjut')==='semua' ? 'selected':'' }}>Semua Status</option>
            <option value="menunggu"  {{ request('status_tindak_lanjut')==='menunggu'  ? 'selected':'' }}>Menunggu Review</option>
            <option value="ditangani" {{ request('status_tindak_lanjut')==='ditangani' ? 'selected':'' }}>Sedang Ditangani</option>
            <option value="selesai"   {{ request('status_tindak_lanjut')==='selesai'   ? 'selected':'' }}>Selesai Diperbaiki</option>
            <option value="diabaikan" {{ request('status_tindak_lanjut')==='diabaikan' ? 'selected':'' }}>Diabaikan</option>
        </select>

        <div class="search-input-wrap" style="flex:1;min-width:160px;">
            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" name="search" placeholder="Cari barang / petugas..."
                   value="{{ request('search') }}" class="filter-input search-field">
        </div>

        <button type="submit" class="btn-filter">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            Cari
        </button>

        @if(request()->hasAny(['kategori','lokasi','status_tindak_lanjut','search']))
        <a href="{{ route('admin.review.index') }}" class="btn-reset">Reset Filter</a>
        @endif
    </form>
</div>

{{-- Tabel Temuan --}}
<div class="card">
    <div class="card-header">
        <div class="total-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            Daftar Temuan Masalah &nbsp;<strong>{{ $temuan->total() }}</strong>
        </div>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Lokasi</th>
                    <th>Petugas</th>
                    <th>Status Penanganan</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($temuan as $i => $t)
                <tr>
                    <td class="text-muted text-sm">{{ $temuan->firstItem() + $i }}</td>
                    <td style="font-size:0.8rem;color:var(--text-muted);">
                        {{ $t->checked_at ? \Carbon\Carbon::parse($t->checked_at)->translatedFormat('d M Y, H:i') : '—' }}
                    </td>
                    <td><span class="kode-barang">{{ $t->jadwal->barang->kode_barang ?? '—' }}</span></td>
                    <td>
                        <div style="font-weight:600;font-size:0.85rem;color:var(--text-main);">{{ $t->jadwal->barang->nama_barang ?? '—' }}</div>
                        @if($t->jadwal->barang->kategori ?? null)
                            <span class="badge-jenis" style="background:{{ $t->jadwal->barang->kategori->warna }}20;color:{{ $t->jadwal->barang->kategori->warna }};border:1px solid {{ $t->jadwal->barang->kategori->warna }}40;font-size:0.66rem;">
                                {{ $t->jadwal->barang->kategori->nama }}
                            </span>
                        @endif
                    </td>
                    <td style="font-size:0.8rem;color:var(--text-muted);">{{ $t->jadwal->barang->lokasiRelasi->nama ?? '—' }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:26px;height:26px;border-radius:50%;background:linear-gradient(135deg,#1e40af,#3b82f6);color:white;display:flex;align-items:center;justify-content:center;font-size:0.68rem;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr($t->user->name ?? '?',0,1)) }}
                            </div>
                            <span style="font-size:0.8rem;color:var(--text-main);">{{ $t->user->name ?? '—' }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge-status" style="background:{{ $t->statusTindakLanjutColor() }}20;color:{{ $t->statusTindakLanjutColor() }};border:1px solid {{ $t->statusTindakLanjutColor() }}40;">
                            {{ $t->statusTindakLanjutLabel() }}
                        </span>
                    </td>
                    <td>
                        <div class="aksi-group" style="justify-content:center;">
                            <a href="{{ route('admin.review.show', $t->id) }}" class="btn-aksi btn-aksi-view" title="Detail & Tindak Lanjut">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-table-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            <p>Tidak ada temuan masalah — semua barang dalam kondisi aman! 🎉</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($temuan->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Menampilkan {{ $temuan->firstItem() }} sampai {{ $temuan->lastItem() }} dari {{ $temuan->total() }} data
        </div>
        <div class="pagination-links">
            @if($temuan->onFirstPage())
                <span class="page-btn page-btn-disabled">‹</span>
            @else
                <a href="{{ $temuan->previousPageUrl() }}" class="page-btn">‹</a>
            @endif
            @foreach($temuan->getUrlRange(max(1,$temuan->currentPage()-2),min($temuan->lastPage(),$temuan->currentPage()+2)) as $page => $url)
                @if($page == $temuan->currentPage())
                    <span class="page-btn page-btn-active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                @endif
            @endforeach
            @if($temuan->hasMorePages())
                <a href="{{ $temuan->nextPageUrl() }}" class="page-btn">›</a>
            @else
                <span class="page-btn page-btn-disabled">›</span>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection

@push('styles')
<style>
.rev-stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 14px;
    margin-bottom: 20px;
}
.rev-stat-card {
    background: white;
    border: 1px solid var(--border);
    border-top: 3px solid;
    border-radius: 12px;
    padding: 16px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: var(--shadow);
}
.rev-stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.rev-stat-icon svg { width: 20px; height: 20px; }
.rev-stat-val { font-size: 1.6rem; font-weight: 800; color: var(--text-main); line-height: 1.1; }
.rev-stat-label { font-size: 0.78rem; color: var(--text-muted); margin-top: 2px; }
</style>
@endpush

@push('scripts')
<script>
setTimeout(() => {
    const a = document.getElementById('flashAlert');
    if (a) a.style.opacity = '0', setTimeout(() => a.remove(), 400);
}, 5000);
</script>
@endpush