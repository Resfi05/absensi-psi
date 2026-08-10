@extends('layouts.admin')

@section('title', 'Jadwal Pengecekan')
@section('page-title', 'Jadwal Pengecekan')

@section('content')

{{-- FORM HIDDEN UNTUK BULK DELETE TABEL UTAMA --}}
<form id="bulkDeleteForm" method="POST" action="{{ route('admin.jadwal.bulk-delete') }}" style="display: none;">
    @csrf
</form>

<div class="page-header">
    <div class="page-header-left">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-sep">/</span>
            <span class="breadcrumb-current">Jadwal Pengecekan</span>
        </nav>
        <h2 class="page-heading">Jadwal <span class="heading-accent">Pengecekan</span></h2>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <button type="button" id="btnBulkDelete" onclick="submitBulkDelete()" class="btn-primary" style="display:none; background-color:#ef4444; border-color:#ef4444; box-shadow:0 4px 12px rgba(239, 68, 68, 0.2);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                <path d="M10 11v6M14 11v6"/>
            </svg>
            Hapus Terpilih (<span id="bulkCount">0</span>)
        </button>

        <button type="button" class="btn-secondary" onclick="bukaModalHapusLanjutan()" 
                style="background:#dc2626; color:white; border-color:#b91c1c; box-shadow:0 4px 10px rgba(220,38,38,0.2);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:4px;">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                <path d="M10 11v6M14 11v6"/>
            </svg>
            Hapus Lanjutan (Lintas Bulan)
        </button>

        <form action="{{ route('admin.jadwal.perpanjang') }}" method="POST" style="display:inline;" 
              onsubmit="return confirm('✨ OTOMATIS GENERATE 1 TAHUN:\n\nSistem akan membaca jadwal terakhir tiap barang dan langsung membuatkan jadwal hingga akhir Tahun Depan secara otomatis.\n\nLanjutkan?')">
            @csrf
            <button type="submit" class="btn-secondary" style="background:#f59e0b; color:white; border-color:#d97706; box-shadow:0 4px 10px rgba(245,158,11,0.2);">
                Perpanjang 1 Tahun
            </button>
        </form>

        <a href="{{ route('admin.jadwal.create') }}" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Buat Jadwal
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" id="flashAlert">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    {{ session('success') }}
    <button class="alert-close" onclick="this.parentElement.remove()">×</button>
</div>
@endif
@if(session('error'))
<div class="alert alert-error" id="flashAlertError">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    {{ session('error') }}
    <button class="alert-close" onclick="this.parentElement.remove()">×</button>
</div>
@endif

@if($barang_tanpa_lokasi > 0)
<div style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:14px 18px;margin-bottom:20px;display:flex;gap:12px;align-items:flex-start;">
    <svg viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" style="width:20px;height:20px;flex-shrink:0;margin-top:1px;">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    <div>
        <div style="font-size:0.85rem;font-weight:700;color:#92400e;">{{ $barang_tanpa_lokasi }} barang belum punya lokasi</div>
        <div style="font-size:0.8rem;color:#92400e;margin-top:2px;">
            Barang ini tidak akan ikut dijadwalkan sampai lokasinya dilengkapi di
            <a href="{{ route('admin.barang.index') }}" style="font-weight:700;text-decoration:underline;">Kelola Data Barang</a>.
        </div>
    </div>
</div>
@endif

<div class="jadwal-stats">
    <div class="jadwal-stat">
        <div class="jadwal-stat-icon" style="background:#dbeafe;color:#2563eb;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
        </div>
        <div class="jadwal-stat-body">
            <div class="jadwal-stat-val">{{ $total }}</div>
            <div class="jadwal-stat-label">Total Jadwal</div>
            <div class="jadwal-stat-sub">Semua Jadwal</div>
        </div>
    </div>

    <div class="jadwal-stat">
        <div class="jadwal-stat-icon" style="background:#dcfce7;color:#16a34a;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>
        <div class="jadwal-stat-body">
            <div class="jadwal-stat-val">{{ $selesai }}</div>
            <div class="jadwal-stat-label">Jadwal Selesai</div>
            <div class="jadwal-stat-sub">Sudah Dikerjakan</div>
        </div>
    </div>

    <div class="jadwal-stat">
        <div class="jadwal-stat-icon" style="background:#fef3c7;color:#d97706;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <div class="jadwal-stat-body">
            <div class="jadwal-stat-val">{{ $jatuh_tempo }}</div>
            <div class="jadwal-stat-label">Akan Jatuh Tempo</div>
            <div class="jadwal-stat-sub">Dalam 7 Hari</div>
        </div>
    </div>

    <div class="jadwal-stat">
        <div class="jadwal-stat-icon" style="background:#fee2e2;color:#ef4444;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
                <line x1="10" y1="14" x2="14" y2="18"/><line x1="14" y1="14" x2="10" y2="18"/>
            </svg>
        </div>
        <div class="jadwal-stat-body">
            <div class="jadwal-stat-val">{{ $terlambat }}</div>
            <div class="jadwal-stat-label">Terlambat</div>
            <div class="jadwal-stat-sub">Jadwal Terlewat</div>
        </div>
    </div>
</div>

<div class="jadwal-main-grid" style="grid-template-columns: minmax(0,1fr) 280px;">
    <div class="jadwal-table-col">
        <div class="card" style="margin-bottom:16px;">
            <form method="GET" action="{{ route('admin.jadwal.index') }}" class="jadwal-filter-form">
                <select name="kategori" class="filter-select" onchange="this.form.submit()">
                    <option value="semua" {{ !request('kategori') || request('kategori')==='semua' ? 'selected':'' }}>Semua Kategori</option>
                    @foreach($kategori_list as $k)
                        <option value="{{ $k->id }}" {{ request('kategori') == $k->id ? 'selected':'' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>

                <select name="lokasi" class="filter-select" onchange="this.form.submit()">
                    <option value="">Semua Lokasi</option>
                    @foreach($lokasi_list as $lok)
                        <option value="{{ $lok->id }}" {{ request('lokasi') == $lok->id ? 'selected':'' }}>{{ $lok->nama }}</option>
                    @endforeach
                </select>

                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="semua"       {{ !request('status') || request('status')==='semua' ? 'selected':'' }}>Semua Status</option>
                    <option value="selesai"     {{ request('status')==='selesai'     ? 'selected':'' }}>Selesai</option>
                    <option value="jatuh_tempo" {{ request('status')==='jatuh_tempo' ? 'selected':'' }}>Jatuh Tempo</option>
                    <option value="terlambat"   {{ request('status')==='terlambat'   ? 'selected':'' }}>Terlambat</option>
                </select>

                <div style="flex:0 0 160px;">
                    <input type="month" name="bulan" value="{{ $bulan }}"
                           class="filter-input" style="padding-left:12px;height:40px;width:100%;"
                           onchange="this.form.submit()">
                </div>

                <div class="search-input-wrap" style="flex:1;min-width:160px;">
                    <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" name="search" placeholder="Cari barang / lokasi..."
                           value="{{ request('search') }}" class="filter-input search-field">
                </div>

                <button type="submit" class="btn-filter">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Cari
                </button>

                <a href="{{ request()->fullUrlWithQuery(['export'=>'excel']) }}" class="btn-export">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/>
                    </svg>
                    Export
                </a>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="total-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Daftar Jadwal Pengecekan &nbsp;<strong>{{ $jadwal->total() }}</strong>
                </div>
            </div>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">
                                <input type="checkbox" id="selectAllCheckbox" style="cursor: pointer; width: 16px; height: 16px;">
                            </th>
                            <th>No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Lokasi</th>
                            <th>Frekuensi</th>
                            <th>Jadwal</th>
                            <th>Status</th>
                            <th style="text-align:center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwal as $i => $item)
                        <tr>
                            <td style="text-align: center;">
                                @if($item->status !== 'selesai')
                                    <input type="checkbox" class="row-checkbox" value="{{ $item->id }}" style="cursor: pointer; width: 16px; height: 16px;">
                                @else
                                    <input type="checkbox" disabled title="Jadwal Selesai tidak bisa dihapus massal" style="opacity: 0.4; cursor: not-allowed; width: 16px; height: 16px;">
                                @endif
                            </td>
                            <td class="text-muted text-sm">{{ $jadwal->firstItem() + $i }}</td>
                            <td>
                                <span style="font-family:monospace;font-size:0.8rem;font-weight:700;color:#2563eb;background:#eff6ff;padding:2px 8px;border-radius:5px;">
                                    {{ $item->barang->kode_barang }}
                                </span>
                            </td>
                            <td>
                                <div style="font-weight:600;font-size:0.875rem;color:var(--text-main);">
                                    {{ $item->barang->nama_barang }}
                                </div>
                            </td>
                            <td>
                                @if($item->barang->kategori)
                                    <span class="badge-jenis"
                                          style="background:{{ $item->barang->kategori->warna }}20;color:{{ $item->barang->kategori->warna }};border:1px solid {{ $item->barang->kategori->warna }}40;">
                                        {{ $item->barang->kategori->nama }}
                                    </span>
                                @else
                                    <span class="badge-jenis badge-gray">{{ $item->barang->jenis_barang }}</span>
                                @endif
                            </td>
                            <td style="font-size:0.82rem;color:var(--text-muted);">{{ $item->barang->lokasi }}</td>
                            <td>
                                <span class="jadwal-frek-badge">{{ $item->frekuensiLabel() }}</span>
                            </td>
                            <td>
                                @php
                                    $tgl    = \Carbon\Carbon::parse($item->tanggal_jadwal);
                                    $isLate = $tgl->isPast() && $item->status === 'pending';
                                    $isSoon = !$tgl->isPast() && $tgl->diffInDays(now()) <= 7 && $item->status === 'pending';
                                @endphp
                                <div style="font-size:0.82rem;font-weight:600;color:{{ $isLate ? '#ef4444' : ($isSoon ? '#d97706' : 'var(--text-main)') }};">
                                    {{ $tgl->translatedFormat('d M Y') }}
                                </div>
                                <div style="font-size:0.72rem;color:var(--text-muted);">
                                    {{ $item->status === 'selesai' ? 'Selesai' : $tgl->diffForHumans() }}
                                </div>
                            </td>
                            <td>
                                @if($item->status === 'selesai')
                                    <span class="badge-status badge-green">Selesai</span>
                                @elseif($item->status === 'terlambat')
                                    <span class="badge-status badge-red">Terlambat</span>
                                @else
                                    <span class="badge-status badge-orange">Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="aksi-group" style="justify-content:center;">
                                    <a href="{{ route('admin.jadwal.show', $item->id) }}"
                                       class="btn-aksi btn-aksi-view" title="Detail & Edit">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.jadwal.destroy', $item->id) }}"
                                          onsubmit="return confirm('Hapus jadwal ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-aksi btn-aksi-delete" title="Hapus">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                                                <path d="M10 11v6M14 11v6"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10">
                                <div class="empty-table-state">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                    <p>Belum ada jadwal untuk filter ini</p>
                                    <a href="{{ route('admin.jadwal.create') }}" class="btn-primary btn-sm">+ Buat Jadwal</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($jadwal->hasPages())
            <div class="pagination-wrap">
                <div class="pagination-info">
                    Menampilkan {{ $jadwal->firstItem() }} sampai {{ $jadwal->lastItem() }} dari {{ $jadwal->total() }} data
                </div>
                <div class="pagination-links">
                    @if($jadwal->onFirstPage())
                        <span class="page-btn page-btn-disabled">««</span>
                        <span class="page-btn page-btn-disabled">‹</span>
                    @else
                        <a href="{{ $jadwal->url(1) }}" class="page-btn">««</a>
                        <a href="{{ $jadwal->previousPageUrl() }}" class="page-btn">‹</a>
                    @endif
                    @foreach($jadwal->getUrlRange(max(1,$jadwal->currentPage()-2),min($jadwal->lastPage(),$jadwal->currentPage()+2)) as $page => $url)
                        @if($page == $jadwal->currentPage())
                            <span class="page-btn page-btn-active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                        @endif
                    @endforeach
                    @if($jadwal->hasMorePages())
                        <a href="{{ $jadwal->nextPageUrl() }}" class="page-btn">›</a>
                        <a href="{{ $jadwal->url($jadwal->lastPage()) }}" class="page-btn">»»</a>
                    @else
                        <span class="page-btn page-btn-disabled">›</span>
                        <span class="page-btn page-btn-disabled">»»</span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="jadwal-side-col">
        <div class="card">
            {{-- 🔥 HEADER KALENDER YANG SUDAH DIRAPIKAN 🔥 --}}
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; margin-bottom: 10px;">
                <h3 class="card-title" style="margin: 0; font-size: 1.05rem; color: var(--text-main);">Kalender Jadwal</h3>
                
                <div style="display: flex; align-items: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 2px;">
                    @php
                        $prevBulan = \Carbon\Carbon::createFromDate($tahun, $bln, 1)->subMonth()->format('Y-m');
                        $nextBulan = \Carbon\Carbon::createFromDate($tahun, $bln, 1)->addMonth()->format('Y-m');
                    @endphp
                    
                    {{-- Tombol Prev --}}
                    <a href="{{ route('admin.jadwal.index', array_merge(request()->query(), ['bulan'=>$prevBulan])) }}" 
                       style="display: flex; align-items: center; justify-content: center; width: 30px; height: 30px; color: #64748b; border-radius: 6px; text-decoration: none; transition: 0.2s;"
                       onmouseover="this.style.background='#e2e8f0'; this.style.color='#0f172a'"
                       onmouseout="this.style.background='transparent'; this.style.color='#64748b'" title="Bulan Sebelumnya">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 16px; height: 16px;"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </a>
                    
                    {{-- Input Pemilih Bulan --}}
                    <input type="month" value="{{ $bulan }}" onchange="changeKalenderBulan(this.value)" 
                           style="border: none; background: transparent; font-family: inherit; font-size: 0.8rem; font-weight: 700; color: #334155; text-align: center; cursor: pointer; outline: none; padding: 0 8px; width: 120px;">
                           
                    {{-- Tombol Next --}}
                    <a href="{{ route('admin.jadwal.index', array_merge(request()->query(), ['bulan'=>$nextBulan])) }}" 
                       style="display: flex; align-items: center; justify-content: center; width: 30px; height: 30px; color: #64748b; border-radius: 6px; text-decoration: none; transition: 0.2s;"
                       onmouseover="this.style.background='#e2e8f0'; this.style.color='#0f172a'"
                       onmouseout="this.style.background='transparent'; this.style.color='#64748b'" title="Bulan Berikutnya">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 16px; height: 16px;"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                </div>
            </div>
            
            <div style="padding:12px;">
                <div class="kal-header">
                    @foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $h)
                        <div class="kal-head-cell">{{ $h }}</div>
                    @endforeach
                </div>
                @php
                    $startOfMonth = \Carbon\Carbon::createFromDate($tahun, $bln, 1)->startOfMonth();
                    $endOfMonth   = \Carbon\Carbon::createFromDate($tahun, $bln, 1)->endOfMonth();
                    $startGrid    = $startOfMonth->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
                    $endGrid      = $endOfMonth->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
                    $current      = $startGrid->copy();
                @endphp
                <div class="kal-grid">
                    @while($current->lte($endGrid))
                        @php
                            $dateKey       = $current->format('Y-m-d');
                            $isToday       = $current->isToday();
                            $isMonth       = $current->month == $bln;
                            $events        = $kalender[$dateKey] ?? collect();
                            $hasSelesai    = $events->where('status','selesai')->sum('jumlah') > 0;
                            $hasTerlambat  = $events->where('status','terlambat')->sum('jumlah') > 0;
                            $hasPending    = $events->where('status','pending')->sum('jumlah') > 0;
                            $total_ev      = $events->sum('jumlah');
                            $isClickable   = $total_ev > 0 && $isMonth;
                            
                            $isFilteredOut = isset($isFiltering) && $isFiltering && !in_array($dateKey, $highlightDates);
                        @endphp
                        <div class="kal-cell {{ !$isMonth ? 'kal-cell-out' : '' }} {{ $isToday ? 'kal-cell-today' : '' }} {{ $hasTerlambat ? 'kal-cell-late' : ($hasPending && $current->copy()->startOfDay()->isPast() ? 'kal-cell-warn' : '') }}"
                             style="{{ $isFilteredOut ? 'opacity: 0.15; filter: grayscale(100%); pointer-events: none;' : '' }} {{ $isClickable && !$isFilteredOut ? 'cursor:pointer;' : '' }}"
                             @if($isClickable && !$isFilteredOut)
                                 onclick="bukaDetailTanggal('{{ $dateKey }}', '{{ $current->translatedFormat('d F Y') }}')"
                                 title="Lihat jadwal {{ $current->translatedFormat('d M Y') }}"
                             @endif>
                            <span class="kal-date">{{ $current->day }}</span>
                            @if($total_ev > 0 && $isMonth)
                                <div class="kal-dots">
                                    @if($hasSelesai)   <span class="kal-dot kal-dot-green"></span>  @endif
                                    @if($hasPending)   <span class="kal-dot kal-dot-orange"></span> @endif
                                    @if($hasTerlambat) <span class="kal-dot kal-dot-red"></span>    @endif
                                </div>
                                <div class="kal-count">{{ $total_ev }}</div>
                            @endif
                        </div>
                        @php $current->addDay(); @endphp
                    @endwhile
                </div>
                <div class="kal-legend" style="margin-top:10px;">
                    <div class="kal-legend-item"><span class="kal-dot kal-dot-green"></span> Selesai</div>
                    <div class="kal-legend-item"><span class="kal-dot kal-dot-orange"></span> Jatuh Tempo</div>
                    <div class="kal-legend-item"><span class="kal-dot kal-dot-red"></span> Terlambat</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="modalKalender" onclick="this.classList.remove('active')">
    <div class="modal-box" style="max-width:480px" onclick="event.stopPropagation()">
        <div class="modal-header">
            <div>
                <div class="modal-title" style="font-family:inherit;font-size:1rem;" id="kalModalTitle">
                    Jadwal Tanggal
                </div>
                <div class="modal-sub" id="kalModalSub">Daftar jadwal pengecekan</div>
            </div>
            <button class="modal-close" onclick="document.getElementById('modalKalender').classList.remove('active')">×</button>
        </div>
        <div class="modal-body" style="flex-direction:column;gap:0;align-items:stretch;padding:0;max-height:60vh;overflow-y:auto;" id="kalModalBody">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary"
                    onclick="document.getElementById('modalKalender').classList.remove('active')">Tutup</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="modalHapusLanjutan" onclick="this.classList.remove('active')">
    <div class="modal-box" style="max-width:850px; width:90%;" onclick="event.stopPropagation()">
        <div class="modal-header" style="background:#fef2f2; border-bottom:1px solid #fee2e2;">
            <div>
                <div class="modal-title" style="color:#991b1b; font-size:1.1rem; font-weight:700;">
                    🗑️ Hapus Jadwal Lanjutan (Tanpa Batas Bulan)
                </div>
                <div class="modal-sub" style="color:#7f1d1d;">
                    Pilih Kategori/Lokasi untuk menampilkan seluruh jadwal lintas periode dan hapus sekaligus.
                </div>
            </div>
            <button class="modal-close" onclick="document.getElementById('modalHapusLanjutan').classList.remove('active')">×</button>
        </div>

        <form method="POST" action="{{ route('admin.jadwal.bulk-delete-advanced') }}" id="formHapusLanjutan" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal yang dicentang?')">
            @csrf
            <div class="modal-body" style="padding:20px; display:flex; flex-direction:column; gap:16px;">
                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:10px; background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #e2e8f0;">
                    <div>
                        <label style="font-size:0.75rem; font-weight:700; color:#475569;">Kategori</label>
                        <select id="modalKatFilter" class="filter-select" style="width:100%; margin-top:4px;" onchange="loadJadwalDeleteData()">
                            <option value="semua">Semua Kategori</option>
                            @foreach($kategori_list as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size:0.75rem; font-weight:700; color:#475569;">Lokasi</label>
                        <select id="modalLokFilter" class="filter-select" style="width:100%; margin-top:4px;" onchange="loadJadwalDeleteData()">
                            <option value="semua">Semua Lokasi</option>
                            @foreach($lokasi_list as $lok)
                                <option value="{{ $lok->id }}">{{ $lok->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size:0.75rem; font-weight:700; color:#475569;">Cari Barang</label>
                        <input type="text" id="modalSearchFilter" placeholder="Nama / Kode..." class="filter-input" style="width:100%; margin-top:4px; height:38px;" onkeyup="loadJadwalDeleteData()">
                    </div>
                </div>

                <div style="max-height:350px; overflow-y:auto; border:1px solid #e2e8f0; border-radius:8px;">
                    <table class="data-table" style="width:100%; font-size:0.82rem;">
                        <thead style="position:sticky; top:0; background:#f1f5f9; z-index:2;">
                            <tr>
                                <th style="width:40px; text-align:center;">
                                    <input type="checkbox" id="modalSelectAll" onchange="toggleModalCheckboxes(this)">
                                </th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Tanggal Jadwal</th>
                            </tr>
                        </thead>
                        <tbody id="modalTableBody">
                            <tr>
                                <td colspan="6" style="text-align:center; padding:20px; color:#94a3b8;">
                                    Silakan sesuaikan filter di atas untuk memuat jadwal...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="padding:12px 20px; background:#f8fafc; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
                <div style="font-size:0.82rem; color:#64748b;">
                    Terpilih: <strong id="modalSelectedCount" style="color:#ef4444;">0</strong> jadwal
                </div>
                <div style="display:flex; gap:8px;">
                    <button type="button" class="btn-secondary" onclick="document.getElementById('modalHapusLanjutan').classList.remove('active')">Batal</button>
                    <button type="submit" id="btnSubmitModalDelete" class="btn-primary" style="background:#dc2626; border-color:#b91c1c;" disabled>
                        Hapus Jadwal Terpilih
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
const selectAllCheckbox = document.getElementById('selectAllCheckbox');
const rowCheckboxes     = document.querySelectorAll('.row-checkbox');
const btnBulkDelete     = document.getElementById('btnBulkDelete');
const bulkCountSpan     = document.getElementById('bulkCount');
const bulkDeleteForm    = document.getElementById('bulkDeleteForm');

function updateBulkButtonState() {
    const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
    if (checkedCount > 0) {
        btnBulkDelete.style.display = 'inline-flex';
        bulkCountSpan.textContent = checkedCount;
    } else {
        btnBulkDelete.style.display = 'none';
    }
}

if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function() {
        rowCheckboxes.forEach(cb => {
            if (!cb.disabled) cb.checked = selectAllCheckbox.checked;
        });
        updateBulkButtonState();
    });
}

rowCheckboxes.forEach(cb => {
    cb.addEventListener('change', function() {
        if (!this.checked && selectAllCheckbox) selectAllCheckbox.checked = false;
        updateBulkButtonState();
    });
});

function submitBulkDelete() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    if (checkedBoxes.length === 0) return;

    if (confirm(`Anda yakin ingin menghapus ${checkedBoxes.length} jadwal yang dipilih secara massal?\n\n(Jadwal yang terhapus tidak dapat dikembalikan)`)) {
        bulkDeleteForm.innerHTML = '@csrf'; 
        
        checkedBoxes.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = cb.value;
            bulkDeleteForm.appendChild(input);
        });

        bulkDeleteForm.submit();
    }
}

function changeKalenderBulan(val) {
    if (!val) return;
    const url = new URL(window.location.href);
    url.searchParams.set('bulan', val);
    window.location.href = url.toString();
}

function bukaDetailTanggal(tanggal, labelTanggal) {
    const modal = document.getElementById('modalKalender');
    const body  = document.getElementById('kalModalBody');
    const title = document.getElementById('kalModalTitle');
    const sub   = document.getElementById('kalModalSub');

    title.textContent = 'Jadwal ' + labelTanggal;
    sub.textContent   = 'Memuat...';
    body.innerHTML    = '<div style="padding:24px;text-align:center;color:#94a3b8;">⏳ Memuat jadwal...</div>';
    modal.classList.add('active');

    fetch(`{{ route('admin.jadwal.kalender-detail') }}?tanggal=${tanggal}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        sub.textContent = data.length + ' jadwal ditemukan';

        if (data.length === 0) {
            body.innerHTML = '<div style="padding:24px;text-align:center;color:#94a3b8;">Tidak ada jadwal di tanggal ini.</div>';
            return;
        }

        body.innerHTML = data.map(j => {
            const badgeColor = j.status === 'selesai' ? '#16a34a' : j.status === 'terlambat' ? '#ef4444' : '#d97706';
            const badgeLabel = j.status === 'selesai' ? 'Selesai' : j.status === 'terlambat' ? 'Terlambat' : 'Aktif';
            const badgeBg    = j.status === 'selesai' ? '#dcfce7' : j.status === 'terlambat' ? '#fee2e2' : '#fef3c7';

            return `
            <a href="${j.url}" style="display:flex;align-items:center;gap:12px;padding:14px 18px;border-bottom:1px solid #f1f5f9;text-decoration:none;transition:background 0.15s;"
               onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                <div style="width:38px;height:38px;border-radius:10px;background:${j.warna}18;color:${j.warna};
                            display:flex;align-items:center;justify-content:center;flex-shrink:0;font-weight:800;font-size:0.72rem;">
                    ${j.kategori.substring(0,2)}
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:0.85rem;font-weight:700;color:#1e293b;margin-bottom:2px;">
                        ${j.nama}
                        <span style="font-family:monospace;font-size:0.72rem;color:#2563eb;background:#eff6ff;padding:1px 6px;border-radius:4px;margin-left:4px;">${j.kode}</span>
                    </div>
                    <div style="font-size:0.75rem;color:#64748b;">
                        📍 ${j.lokasi} ${j.jam ? '• ⏰ ' + j.jam : ''} • 🔄 ${j.frekuensi}
                    </div>
                </div>
                <span style="font-size:0.72rem;font-weight:700;color:${badgeColor};background:${badgeBg};
                             padding:3px 9px;border-radius:10px;flex-shrink:0;">
                    ${badgeLabel}
                </span>
            </a>`;
        }).join('');
    })
    .catch(() => {
        body.innerHTML = '<div style="padding:24px;text-align:center;color:#ef4444;">Gagal memuat data. Coba lagi.</div>';
        sub.textContent = 'Error';
    });
}

function bukaModalHapusLanjutan() {
    document.getElementById('modalHapusLanjutan').classList.add('active');
    loadJadwalDeleteData();
}

function loadJadwalDeleteData() {
    const kat = document.getElementById('modalKatFilter').value;
    const lok = document.getElementById('modalLokFilter').value;
    const search = document.getElementById('modalSearchFilter').value;
    const tbody = document.getElementById('modalTableBody');

    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:20px; color:#94a3b8;">⏳ Memuat data jadwal...</td></tr>';

    fetch(`{{ route('admin.jadwal.get-delete-data') }}?kategori=${kat}&lokasi=${lok}&search=${encodeURIComponent(search)}`)
        .then(res => res.json())
        .then(data => {
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:20px; color:#94a3b8;">Tidak ada jadwal aktif/pending yang ditemukan.</td></tr>';
                updateModalCount();
                return;
            }

            tbody.innerHTML = data.map(j => `
                <tr>
                    <td style="text-align:center;">
                        <input type="checkbox" name="ids[]" value="${j.id}" class="modal-item-checkbox" onchange="updateModalCount()">
                    </td>
                    <td><span style="font-family:monospace; font-weight:700; color:#2563eb;">${j.kode_barang}</span></td>
                    <td><strong>${j.nama_barang}</strong></td>
                    <td>${j.kategori}</td>
                    <td>${j.lokasi}</td>
                    <td><span style="color:#dc2626; font-weight:600;">${j.tanggal}</span></td>
                </tr>
            `).join('');

            document.getElementById('modalSelectAll').checked = false;
            updateModalCount();
        })
        .catch(() => {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:20px; color:#ef4444;">Gagal mengambil data.</td></tr>';
        });
}

function toggleModalCheckboxes(master) {
    const cbs = document.querySelectorAll('.modal-item-checkbox');
    cbs.forEach(cb => cb.checked = master.checked);
    updateModalCount();
}

function updateModalCount() {
    const checked = document.querySelectorAll('.modal-item-checkbox:checked').length;
    document.getElementById('modalSelectedCount').textContent = checked;
    document.getElementById('btnSubmitModalDelete').disabled = checked === 0;
}

setTimeout(() => {
    const a = document.getElementById('flashAlert');
    if (a) a.style.opacity = '0', setTimeout(() => a.remove(), 400);
    const e = document.getElementById('flashAlertError');
    if (e) e.style.opacity = '0', setTimeout(() => e.remove(), 400);
}, 5000);
</script>
@endpush