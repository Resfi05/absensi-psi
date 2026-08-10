@extends('layouts.admin')

@section('title', 'Generate QR Code')
@section('page-title', 'Generate QR Code')

@section('content')

{{-- 🚀 HEADER 🚀 --}}
<div class="page-header">
    <div class="page-header-left">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-sep">/</span>
            <span class="breadcrumb-current">Generate QR Code</span>
        </nav>
        <h2 class="page-heading">Generate <span class="heading-accent">QR Code</span></h2>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <form method="POST" action="{{ route('admin.qrcode.generate-all') }}"
              onsubmit="return confirm('Generate QR untuk semua barang yang belum punya QR Code?')">
            @csrf
            <button type="submit" class="btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="3" height="3"/>
                </svg>
                Generate Semua
            </button>
        </form>
    </div>
</div>

{{-- 🚨 FLASH 🚨 --}}
@if(session('success'))
<div class="alert alert-success" id="flashAlert">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    {{ session('success') }}
    <button class="alert-close" onclick="this.parentElement.remove()">✕</button>
</div>
@endif
@if(session('info'))
<div class="alert" style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;margin-bottom:20px" id="flashAlert">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    {{ session('info') }}
    <button class="alert-close" onclick="this.parentElement.remove()">✕</button>
</div>
@endif

{{-- 📊 STAT CARDS 📊 --}}
<div class="qr-stats">
    <div class="qr-stat-card qr-stat-blue">
        <div class="qr-stat-icon">
            {{-- REVISI 1: Ikon "Total Barang" Diubah Menjadi Gambar Box/Barang --}}
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                <line x1="12" y1="22.08" x2="12" y2="12"/>
            </svg>
        </div>
        <div>
            <div class="qr-stat-val">{{ $total_all }}</div>
            <div class="qr-stat-label">Total Barang</div>
        </div>
    </div>
    <div class="qr-stat-card qr-stat-green">
        <div class="qr-stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="3" height="3"/>
            </svg>
        </div>
        <div>
            <div class="qr-stat-val">{{ $total_qr }}</div>
            <div class="qr-stat-label">Sudah Ada QR</div>
        </div>
    </div>
    <div class="qr-stat-card qr-stat-orange">
        <div class="qr-stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>
        <div>
            <div class="qr-stat-val">{{ $total_belum }}</div>
            <div class="qr-stat-label">Belum Ada QR</div>
        </div>
    </div>
</div>

{{-- 🔍 FILTER 🔍 --}}
<div class="filter-bar" style="margin-bottom:20px">
    <form method="GET" action="{{ route('admin.qrcode.index') }}" class="filter-form" style="padding:0;flex:1;">
        <div class="search-input-wrap">
            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" name="search" placeholder="Cari kode / nama barang..."
                   value="{{ request('search') }}" class="filter-input search-field">
        </div>
        
        {{-- REVISI 2: Filter Kategori Diambil Otomatis dari Database Semua Kategori --}}
        <select name="jenis" class="filter-select">
            <option value="semua">Semua Kategori</option>
            @foreach(\App\Models\KategoriBarang::all() as $kat)
                <option value="{{ $kat->nama }}" {{ request('jenis') === $kat->nama ? 'selected' : '' }}>
                    {{ $kat->nama }}
                </option>
            @endforeach
        </select>
        
        <select name="qr_status" class="filter-select">
            <option value="">Semua Status QR</option>
            <option value="sudah" {{ request('qr_status') === 'sudah' ? 'selected' : '' }}>Sudah Ada QR</option>
            <option value="belum" {{ request('qr_status') === 'belum' ? 'selected' : '' }}>Belum Ada QR</option>
        </select>
        <button type="submit" class="btn-filter">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            Cari
        </button>
        @if(request()->hasAny(['search','jenis','qr_status']))
        <a href="{{ route('admin.qrcode.index') }}" class="btn-reset">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>
            </svg>
            Reset
        </a>
        @endif
    </form>
</div>

{{-- 🔳 GRID BARANG 🔳 --}}
<div class="qr-grid">
    @forelse($barang as $item)
    @php
        // REVISI 3: Logika Pengambilan Warna Kategori Dinamis
        $katColor = $item->kategori ? $item->kategori->warna : '#64748b'; // Default ke abu-abu jika kategori dihapus/tidak ada
    @endphp

    <div class="qr-card {{ $item->qrCode ? 'qr-card-has' : 'qr-card-empty' }}">

        {{-- Status badge (Pojok Kanan Atas) --}}
        <div class="qr-card-badge">
            @if($item->qrCode)
                <span class="badge-status badge-green">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:11px;height:11px">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    QR Aktif
                </span>
            @else
                <span class="badge-status badge-orange">Belum Ada QR</span>
            @endif
        </div>

        {{-- Informasi Barang (Kiri) --}}
        <div class="qr-card-top" style="align-items: flex-start;">
            
            <div class="qr-card-info" style="width: 100%;">
                {{-- REVISI 3: KODE/KATEGORI BARANG DENGAN WARNA DINAMIS DARI MASTER --}}
                <div style="
                    display: inline-block;
                    padding: 4px 10px;
                    border-radius: 6px;
                    font-size: 0.75rem;
                    font-weight: 800;
                    letter-spacing: 0.5px;
                    background-color: {{ $katColor }}20; /* Opacity 20% background */
                    color: {{ $katColor }}; /* Warna Teks Solid */
                    border: 1px solid {{ $katColor }}40; /* Opacity 40% border */
                    margin-bottom: 8px;
                ">
                    {{ $item->kategori ? $item->kategori->nama : ($item->jenis_barang ?? $item->kode_barang) }}
                </div>
                
                <div class="qr-card-nama" style="font-size:1rem; font-weight:700; color:var(--text-main); margin-bottom:6px;">
                    {{ $item->nama_barang }}
                </div>
                
                <div class="qr-card-lokasi" style="font-size:0.8rem; color:var(--text-muted); display:flex; align-items:center; gap:5px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;flex-shrink:0">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
                    </svg>
                    {{ $item->lokasiRelasi->nama ?? $item->lokasi }}
                </div>
            </div>
        </div>

        {{-- QR Preview --}}
        <div class="qr-preview-area">
            @if($item->qrCode)
                <img src="{{ asset('storage/' . $item->qrCode->qr_code_path) }}"
                     alt="QR {{ $item->kode_barang }}"
                     class="qr-image">
            @else
                <div class="qr-empty-placeholder">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                        <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="3" height="3"/>
                    </svg>
                    <span>Belum digenerate</span>
                </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="qr-card-actions">
            @if($item->qrCode)
                <a href="{{ route('admin.qrcode.cetak', $item->qrCode->id) }}"
                   target="_blank" class="btn-qr-action btn-qr-cetak">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"/>
                        <path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/>
                        <rect x="6" y="14" width="12" height="8"/>
                    </svg>
                    Cetak
                </a>
                <a href="{{ route('admin.qrcode.download', $item->qrCode->id) }}"
                   class="btn-qr-action btn-qr-download">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Download
                </a>
                <form method="POST" action="{{ route('admin.qrcode.generate', $item->id) }}"
                      onsubmit="return confirm('Generate ulang QR Code untuk {{ $item->kode_barang }}?')">
                    @csrf
                    <button type="submit" class="btn-qr-action btn-qr-regenerate">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>
                        </svg>
                        Ulang
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.qrcode.generate', $item->id) }}" style="width:100%">
                    @csrf
                    <button type="submit" class="btn-qr-action btn-qr-generate-now" style="width:100%">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                            <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="3" height="3"/>
                        </svg>
                        Generate QR Code
                    </button>
                </form>
            @endif
        </div>

    </div>
    @empty
    <div style="grid-column:1/-1">
        <div class="empty-table-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                <rect x="3" y="14" width="7" height="7"/>
            </svg>
            <p>Tidak ada data barang</p>
        </div>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($barang->hasPages())
<div class="card" style="margin-top:16px">
    <div class="pagination-wrap">
        <div class="pagination-info">
            Menampilkan {{ $barang->firstItem() }} sampai {{ $barang->lastItem() }} dari {{ $barang->total() }} barang
        </div>
        <div class="pagination-links">
            @if($barang->onFirstPage())
                <span class="page-btn page-btn-disabled">«</span>
                <span class="page-btn page-btn-disabled">‹</span>
            @else
                <a href="{{ $barang->url(1) }}" class="page-btn">«</a>
                <a href="{{ $barang->previousPageUrl() }}" class="page-btn">‹</a>
            @endif

            @foreach($barang->getUrlRange(max(1,$barang->currentPage()-2), min($barang->lastPage(),$barang->currentPage()+2)) as $page => $url)
                @if($page == $barang->currentPage())
                    <span class="page-btn page-btn-active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                @endif
            @endforeach

            @if($barang->hasMorePages())
                <a href="{{ $barang->nextPageUrl() }}" class="page-btn">›</a>
                <a href="{{ $barang->url($barang->lastPage()) }}" class="page-btn">»</a>
            @else
                <span class="page-btn page-btn-disabled">›</span>
                <span class="page-btn page-btn-disabled">»</span>
            @endif
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
setTimeout(() => {
    const a = document.getElementById('flashAlert');
    if (a) a.style.opacity = '0', setTimeout(() => a.remove(), 400);
}, 4000);
</script>
@endpush