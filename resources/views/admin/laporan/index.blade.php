@extends('layouts.admin')
@section('title', 'Laporan & Export')
@section('page-title', 'Laporan & Export')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-current">Laporan & Export</span>
        </nav>
        <h2 class="page-heading">Laporan <span class="heading-accent">& Export</span></h2>
        <p style="font-size:0.82rem;color:var(--text-muted);margin-top:4px;">Kelola dan unduh laporan hasil pengecekan barang beserta foto buktinya</p>
    </div>

    <div style="display:flex; gap:10px; align-items:center;">
        <button type="button" class="btn-primary" onclick="document.getElementById('modalWipeData').classList.add('active')" 
                style="background:#dc2626; border-color:#b91c1c; box-shadow:0 4px 10px rgba(220,38,38,0.2);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:4px;">
                <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2M10 11v6M14 11v6"/>
            </svg>
            Hapus Total (Wipe Data)
        </button>

        <div style="position:relative;">
            <button onclick="document.getElementById('exportDropdown').classList.toggle('active')" class="btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Export Data
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;margin-left:2px;">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>
            <div id="exportDropdown" class="export-dropdown">
                <a href="{{ route('admin.laporan.excel', request()->query()) }}" class="export-dropdown-item export-dropdown-active">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#16a34a;">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <span>Download Excel</span>
                </a>
                <a href="{{ route('admin.laporan.pdf', request()->query()) }}" class="export-dropdown-item export-dropdown-active">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#ef4444;">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <span>Download PDF</span>
                </a>
                <a href="{{ route('admin.laporan.csv', request()->query()) }}" class="export-dropdown-item export-dropdown-active">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#2563eb;">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <span>Download CSV</span>
                </a>
                <div onclick="printLaporan()" class="export-dropdown-item export-dropdown-active" style="cursor:pointer;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#64748b;">
                        <polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/>
                    </svg>
                    <span>Cetak / Print</span>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" id="flashAlert" style="background:#dcfce7; border:1px solid #bbf7d0; color:#15803d; padding:12px 16px; border-radius:10px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center;">
    <div style="display:flex; align-items:center; gap:8px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;">
            <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    <button onclick="this.parentElement.remove()" style="background:none; border:none; cursor:pointer; font-size:1.1rem; color:#15803d;">×</button>
</div>
@endif
@if(session('error'))
<div class="alert alert-error" id="flashAlertError" style="background:#fee2e2; border:1px solid #fecaca; color:#b91c1c; padding:12px 16px; border-radius:10px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center;">
    <div style="display:flex; align-items:center; gap:8px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <span>{{ session('error') }}</span>
    </div>
    <button onclick="this.parentElement.remove()" style="background:none; border:none; cursor:pointer; font-size:1.1rem; color:#b91c1c;">×</button>
</div>
@endif

<div class="card" style="margin-bottom:20px;">
    <form method="GET" action="{{ route('admin.laporan.index') }}" class="jadwal-filter-form" id="filterForm">
        <div style="flex:0 0 230px;">
            <input type="text" id="dateRangePicker" name="date_range" class="filter-input" style="height:40px;width:100%;" placeholder="Pilih periode" readonly>
            <input type="hidden" name="tanggal_mulai" id="tanggalMulaiInput" value="{{ $tglMulai->format('Y-m-d') }}">
            <input type="hidden" name="tanggal_akhir" id="tanggalAkhirInput" value="{{ $tglAkhir->format('Y-m-d') }}">
        </div>

        <select name="kategori" class="filter-select">
            <option value="semua" {{ !request('kategori') || request('kategori')==='semua' ? 'selected':'' }}>Semua Jenis</option>
            @foreach($kategori_list as $k)
                <option value="{{ $k->id }}" {{ request('kategori') == $k->id ? 'selected':'' }}>{{ $k->nama }}</option>
            @endforeach
        </select>

        <select name="lokasi" class="filter-select">
            <option value="semua" {{ !request('lokasi') || request('lokasi')==='semua' ? 'selected':'' }}>Semua Lokasi</option>
            @foreach($lokasi_list as $lok)
                <option value="{{ $lok->id }}" {{ request('lokasi') == $lok->id ? 'selected':'' }}>{{ $lok->nama }}</option>
            @endforeach
        </select>

        <select name="status" class="filter-select">
            <option value="semua" {{ !request('status') || request('status')==='semua' ? 'selected':'' }}>Semua Status</option>
            <option value="aman"             {{ request('status')==='aman'             ? 'selected':'' }}>Kondisi Aman</option>
            <option value="proses_perbaikan" {{ request('status')==='proses_perbaikan' ? 'selected':'' }}>Proses Perbaikan</option>
            <option value="selesai_ditutup"  {{ request('status')==='selesai_ditutup'  ? 'selected':'' }}>Selesai / Ditutup</option>
            <option value="belum_dicek"      {{ request('status')==='belum_dicek'      ? 'selected':'' }}>Belum Dicek</option>
        </select>

        <select name="tindak_lanjut" class="filter-select">
            <option value="semua" {{ !request('tindak_lanjut') || request('tindak_lanjut')==='semua' ? 'selected':'' }}>Semua Tindak Lanjut</option>
            <option value="menunggu"  {{ request('tindak_lanjut')==='menunggu'  ? 'selected':'' }}>Menunggu</option>
            <option value="ditangani" {{ request('tindak_lanjut')==='ditangani' ? 'selected':'' }}>Ditangani</option>
            <option value="selesai"   {{ request('tindak_lanjut')==='selesai'   ? 'selected':'' }}>Selesai</option>
            <option value="diabaikan" {{ request('tindak_lanjut')==='diabaikan' ? 'selected':'' }}>Diabaikan</option>
            <option value="terlambat" {{ request('tindak_lanjut')==='terlambat' ? 'selected':'' }}>Terlambat</option>
        </select>

        <div class="search-input-wrap" style="flex:1;min-width:160px;">
            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" name="search" placeholder="Cari barang / lokasi..."
                   value="{{ request('search') }}" class="filter-input search-field">
        </div>

        <button type="submit" class="btn-filter">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/>
            </svg>
            Terapkan Filter
        </button>
    </form>
</div>

<div class="lap-stats-row">
    <div class="lap-stat-card" style="border-top-color:#2563eb;">
        <div class="lap-stat-icon" style="background:#dbeafe;color:#2563eb;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
        </div>
        <div>
            <div class="lap-stat-val">{{ $totalPengecekan }}</div>
            <div class="lap-stat-label">Total Pengecekan</div>
            <div class="lap-stat-sub">Semua</div>
        </div>
    </div>
    <div class="lap-stat-card" style="border-top-color:#16a34a;">
        <div class="lap-stat-icon" style="background:#dcfce7;color:#16a34a;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>
        <div>
            <div class="lap-stat-val">{{ $kondisiAman }}</div>
            <div class="lap-stat-label">Kondisi Aman</div>
            <div class="lap-stat-sub">{{ $totalPengecekan > 0 ? round($kondisiAman/$totalPengecekan*100,1) : 0 }}%</div>
        </div>
    </div>
    <div class="lap-stat-card" style="border-top-color:#ef4444;">
        <div class="lap-stat-icon" style="background:#fee2e2;color:#ef4444;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
            </svg>
        </div>
        <div>
            <div class="lap-stat-val">{{ $prosesPerbaikan }}</div>
            <div class="lap-stat-label">Proses Perbaikan</div>
            <div class="lap-stat-sub">{{ $totalPengecekan > 0 ? round($prosesPerbaikan/$totalPengecekan*100,1) : 0 }}%</div>
        </div>
    </div>
    <div class="lap-stat-card" style="border-top-color:#7c3aed;">
        <div class="lap-stat-icon" style="background:#ede9fe;color:#7c3aed;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/>
            </svg>
        </div>
        <div>
            <div class="lap-stat-val">{{ $selesaiDitutup }}</div>
            <div class="lap-stat-label">Selesai / Ditutup</div>
            <div class="lap-stat-sub">{{ $totalPengecekan > 0 ? round($selesaiDitutup/$totalPengecekan*100,1) : 0 }}%</div>
        </div>
    </div>
</div>

<div class="lap-main-grid">
    <div class="card">
        <div class="card-header">
            <div class="total-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                </svg>
                Data Laporan &nbsp;<strong>{{ $laporan->total() }}</strong>
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
                        <th>Oleh</th>
                        <th>Status</th>
                        <th>Tindak Lanjut</th>
                        <th>Foto Bukti</th>
                        <th>Catatan / Temuan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan as $i => $row)
                    @php
                        $jadwal = $row['jadwal'];
                        $p      = $row['pengecekan'];
                        $status = $row['status'];
                        $barang = $jadwal->barang;
                        $statusInfo = match($status) {
                            'aman'             => ['label'=>'Kondisi Aman', 'badge'=>'badge-green'],
                            'proses_perbaikan' => ['label'=>'Proses Perbaikan', 'badge'=>'badge-red'],
                            'selesai_ditutup'  => ['label'=>'Selesai / Ditutup', 'badge'=>'badge-purple'],
                            default            => ['label'=>'Belum Dicek', 'badge'=>'badge-orange'],
                        };
                        
                        // 🔥 REVISI: Logika Pengecekan Foto yang Benar 🔥
                        $fotoBeforeUrl = null;
                        $fotoAfterUrl = null;
                        if ($p) {
                            if ($p->photo_before) {
                                $fotoBeforeUrl = str_starts_with($p->photo_before, 'http') ? $p->photo_before : asset('storage/' . $p->photo_before);
                            }
                            if ($p->photo_after) {
                                $fotoAfterUrl = str_starts_with($p->photo_after, 'http') ? $p->photo_after : asset('storage/' . $p->photo_after);
                            }
                        }
                    @endphp
                    <tr>
                        <td class="text-muted text-sm">{{ $laporan->firstItem() + $i }}</td>
                        <td style="font-size:0.8rem;color:var(--text-muted);">
                            {{ $p && $p->checked_at ? \Carbon\Carbon::parse($p->checked_at)->translatedFormat('d M Y H:i') : \Carbon\Carbon::parse($jadwal->tanggal_jadwal)->translatedFormat('d M Y') }}
                        </td>
                        <td><span class="kode-barang">{{ $barang->kode_barang ?? '???' }}</span></td>
                        <td style="font-weight:600;font-size:0.85rem;color:var(--text-main);">{{ $barang->nama_barang ?? '???' }}</td>
                        <td style="font-size:0.8rem;color:var(--text-muted);">{{ $barang->lokasiRelasi->nama ?? '???' }}</td>
                        <td style="font-size:0.8rem;color:var(--text-muted);">{{ $p->user->name ?? '???' }}</td>
                        
                        <td>
                            <span class="badge-status {{ $statusInfo['badge'] ?? '' }}" 
                                  @if(!isset($statusInfo['badge'])) style="background:#fef3c7; color:#d97706; padding:4px 8px; border-radius:6px; font-size:0.75rem; font-weight:600;" @endif>
                                {{ $statusInfo['label'] }}
                            </span>
                        </td>
                        
                        {{-- 🔥 TAMPILAN TINDAK LANJUT YANG LEBIH JELAS & ADA TANGGAL 🔥 --}}
                        <td>
                            @if($p && $p->status_tindak_lanjut)
                                @php
                                    // Tentukan warna dinamis berdasarkan status
                                    $tlBg = match($p->status_tindak_lanjut) {
                                        'selesai'   => '#dcfce7',
                                        'ditangani' => '#dbeafe',
                                        'menunggu'  => '#fef3c7',
                                        'terlambat' => '#fee2e2',
                                        default     => '#f1f5f9'
                                    };
                                    $tlColor = match($p->status_tindak_lanjut) {
                                        'selesai'   => '#16a34a',
                                        'ditangani' => '#2563eb',
                                        'menunggu'  => '#d97706',
                                        'terlambat' => '#ef4444',
                                        default     => '#64748b'
                                    };
                                    $tlBorder = match($p->status_tindak_lanjut) {
                                        'selesai'   => '#bbf7d0',
                                        'ditangani' => '#bfdbfe',
                                        'menunggu'  => '#fde68a',
                                        'terlambat' => '#fecaca',
                                        default     => '#e2e8f0'
                                    };
                                @endphp
                                <div style="display:flex; flex-direction:column; gap:5px; align-items:flex-start;">
                                    <span style="background:{{ $tlBg }}; color:{{ $tlColor }}; border:1px solid {{ $tlBorder }}; font-size:0.68rem; padding:3px 8px; border-radius:6px; font-weight:800; text-transform:uppercase;">
                                        {{ str_replace('_', ' ', $p->status_tindak_lanjut) }}
                                    </span>
                                    
                                    {{-- Munculkan tanggal update terakhir jika statusnya diproses/selesai --}}
                                    @if(in_array($p->status_tindak_lanjut, ['selesai', 'ditangani', 'diabaikan']) && $p->updated_at)
                                        <span style="font-size:0.65rem; color:#64748b; font-weight:600; display:flex; align-items:center; gap:4px; background:#f8fafc; padding:2px 6px; border-radius:4px; border:1px solid #e2e8f0;" title="Tanggal status diperbarui">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:10px; height:10px;">
                                                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                            </svg>
                                            {{ \Carbon\Carbon::parse($p->updated_at)->translatedFormat('d M Y') }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted text-sm">—</span>
                            @endif
                        </td>
                        
                        {{-- 🔥 TAMPILAN FOTO BUKTI YANG RAPI 🔥 --}}
                        <td>
                            <div style="display:flex; gap:6px; flex-wrap:wrap; align-items:center;">
                                @if($fotoBeforeUrl && $fotoAfterUrl)
                                    {{-- Munculkan 2 Foto (Sebelum & Sesudah) --}}
                                    <div style="position:relative;" title="Foto Sebelum">
                                        <img src="{{ $fotoBeforeUrl }}" class="foto-thumbnail" onclick="previewImage('{{ $fotoBeforeUrl }}', 'Sebelum: {{ e($barang->nama_barang) }}')">
                                        <span style="position:absolute; bottom:0; left:0; right:0; background:rgba(0,0,0,0.6); color:white; font-size:0.55rem; text-align:center; border-radius:0 0 6px 6px;">Seb</span>
                                    </div>
                                    <div style="position:relative;" title="Foto Sesudah">
                                        <img src="{{ $fotoAfterUrl }}" class="foto-thumbnail" onclick="previewImage('{{ $fotoAfterUrl }}', 'Sesudah: {{ e($barang->nama_barang) }}')">
                                        <span style="position:absolute; bottom:0; left:0; right:0; background:rgba(22,163,74,0.8); color:white; font-size:0.55rem; text-align:center; border-radius:0 0 6px 6px;">Ses</span>
                                    </div>
                                @elseif($fotoBeforeUrl)
                                    {{-- Munculkan 1 Foto Saja --}}
                                    <img src="{{ $fotoBeforeUrl }}" class="foto-thumbnail" title="Foto Pengecekan" onclick="previewImage('{{ $fotoBeforeUrl }}', '{{ e($barang->nama_barang) }}')">
                                @elseif($fotoAfterUrl)
                                    {{-- Alternatif jika hanya diisi after --}}
                                    <img src="{{ $fotoAfterUrl }}" class="foto-thumbnail" title="Foto Pengecekan" onclick="previewImage('{{ $fotoAfterUrl }}', '{{ e($barang->nama_barang) }}')">
                                @else
                                    <span style="font-size:0.7rem; color:#94a3b8; font-style:italic; background:#f8fafc; padding:4px 8px; border-radius:4px; border:1px solid #e2e8f0;">Tanpa Foto</span>
                                @endif
                            </div>
                        </td>

                        <td style="font-size:0.78rem;color:var(--text-muted);max-width:200px;">
                            {{ $p && $p->notes ? \Illuminate\Support\Str::limit($p->notes, 60) : '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10">
                            <div class="empty-table-state" style="padding:40px; text-align:center; color:#94a3b8;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:48px; height:48px; margin:0 auto 10px;">
                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                                </svg>
                                <p>Tidak ada data untuk filter ini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($laporan->hasPages())
        <div class="custom-pagination-container">
            <div class="custom-pagination-info">
                Menampilkan <span class="font-bold">{{ $laporan->firstItem() }}</span> - <span class="font-bold">{{ $laporan->lastItem() }}</span> dari <span class="font-bold">{{ $laporan->total() }}</span> data
            </div>
            
            <div class="custom-pagination-links">
                @if($laporan->onFirstPage())
                    <span class="c-page-item disabled"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg></span>
                @else
                    <a href="{{ $laporan->previousPageUrl() }}" class="c-page-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg></a>
                @endif
                
                @foreach($laporan->getUrlRange(max(1, $laporan->currentPage() - 2), min($laporan->lastPage(), $laporan->currentPage() + 2)) as $page => $url)
                    @if($page == $laporan->currentPage())
                        <span class="c-page-item active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="c-page-item">{{ $page }}</a>
                    @endif
                @endforeach
                
                @if($laporan->hasMorePages())
                    <a href="{{ $laporan->nextPageUrl() }}" class="c-page-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg></a>
                @else
                    <span class="c-page-item disabled"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg></span>
                @endif
            </div>
        </div>
        @endif
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ringkasan Laporan</h3>
        </div>
        <div style="padding:20px;display:flex;flex-direction:column;align-items:center;">
            <div style="position:relative;width:190px;height:190px;">
                <canvas id="lapDonutChart"></canvas>
                <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;">
                    <div style="font-size:1.5rem;font-weight:800;color:var(--text-main);">{{ $totalPengecekan }}</div>
                    <div style="font-size:0.72rem;color:var(--text-muted);">Total</div>
                </div>
            </div>
            <div style="width:100%;margin-top:18px;display:flex;flex-direction:column;gap:10px;">
                <div style="display:flex;align-items:center;justify-content:space-between;font-size:0.8rem;">
                    <span style="display:flex;align-items:center;gap:6px;"><span style="width:9px;height:9px;border-radius:50%;background:#16a34a;"></span> Kondisi Aman</span>
                    <span style="font-weight:700;color:var(--text-main);">{{ $kondisiAman }} ({{ $totalPengecekan > 0 ? round($kondisiAman/$totalPengecekan*100,1) : 0 }}%)</span>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;font-size:0.8rem;">
                    <span style="display:flex;align-items:center;gap:6px;"><span style="width:9px;height:9px;border-radius:50%;background:#ef4444;"></span> Proses Perbaikan</span>
                    <span style="font-weight:700;color:var(--text-main);">{{ $prosesPerbaikan }} ({{ $totalPengecekan > 0 ? round($prosesPerbaikan/$totalPengecekan*100,1) : 0 }}%)</span>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;font-size:0.8rem;">
                    <span style="display:flex;align-items:center;gap:6px;"><span style="width:9px;height:9px;border-radius:50%;background:#7c3aed;"></span> Selesai / Ditutup</span>
                    <span style="font-weight:700;color:var(--text-main);">{{ $selesaiDitutup }} ({{ $totalPengecekan > 0 ? round($selesaiDitutup/$totalPengecekan*100,1) : 0 }}%)</span>
                </div>
            </div>
        </div>

        <div style="margin:0 18px 18px;background:#eff6ff;border-radius:10px;padding:14px 16px;">
            <div style="font-size:0.78rem;font-weight:700;color:#2563eb;margin-bottom:4px;">💡 Keterangan</div>
            <div style="font-size:0.76rem;color:#1e40af;line-height:1.5;">
                Laporan berisi data hasil pengecekan sesuai filter yang dipilih. Gunakan tombol Export Data untuk mengunduh laporan lengkap.
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="modalPreviewFoto" onclick="this.classList.remove('active')">
    <div class="modal-box" style="max-width:550px; text-align:center; padding:20px; background:#ffffff;" onclick="event.stopPropagation()">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h4 id="previewTitle" style="font-size:1rem; font-weight:700; color:#1e293b; margin:0;">Foto Bukti Pengecekan</h4>
            <button onclick="document.getElementById('modalPreviewFoto').classList.remove('active')" style="background:none; border:none; cursor:pointer; font-size:1.5rem; color:#64748b;">×</button>
        </div>
        <div style="background:#f8fafc; padding:10px; border-radius:8px; border:1px solid #e2e8f0; display:flex; justify-content:center;">
            <img id="previewImg" src="" style="max-width:100%; max-height:500px; border-radius:6px; object-fit:contain;">
        </div>
    </div>
</div>

<div class="modal-overlay" id="modalWipeData" onclick="this.classList.remove('active')">
    <div class="modal-box" style="max-width:450px;" onclick="event.stopPropagation()">
        <div class="modal-header" style="background:#fef2f2; border-bottom:1px solid #fee2e2;">
            <div>
                <div class="modal-title" style="color:#991b1b; font-size:1.1rem; font-weight:800;">
                    🚨 HAPUS TOTAL DATA (WIPE)
                </div>
                <div class="modal-sub" style="color:#7f1d1d; font-size:0.8rem; margin-top:4px; line-height:1.4;">
                    Tindakan ini akan menghapus Kategori beserta SEMUA Barang, QR Code, Jadwal, Monitoring, dan Laporan terkait. Aksi ini <strong>permanen dan tidak dapat dibatalkan!</strong>
                </div>
            </div>
            <button class="modal-close" onclick="document.getElementById('modalWipeData').classList.remove('active')">×</button>
        </div>

        <form method="POST" action="{{ route('admin.laporan.wipe-data') }}">
            @csrf
            <div class="modal-body" style="padding:20px; display:flex; flex-direction:column; gap:16px;">
                
                <div>
                    <label style="font-size:0.85rem; font-weight:700; color:#475569;">Pilih Kategori yang Akan Dimusnahkan:</label>
                    <select name="kategori_id" class="filter-select" style="width:100%; margin-top:6px;" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategori_list as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="background:#fee2e2; padding:16px; border-radius:8px; border:1px dashed #ef4444;">
                    <p style="font-size:0.82rem; color:#b91c1c; margin-bottom:12px; font-weight:600;">
                        🛡️ Otorisasi Keamanan<br>Masukkan kredensial Anda untuk menyetujui tindakan ini.
                    </p>
                    <div style="margin-bottom:12px;">
                        <input type="text" name="username" class="filter-input" placeholder="Masukkan Username / Nama / Email" style="width:100%; height:40px;" required>
                    </div>
                    <div>
                        <input type="password" name="password" class="filter-input" placeholder="Masukkan Password Anda" style="width:100%; height:40px;" required>
                    </div>
                </div>

            </div>
            <div class="modal-footer" style="padding:12px 20px; background:#f8fafc; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:8px;">
                <button type="button" class="btn-secondary" onclick="document.getElementById('modalWipeData').classList.remove('active')">Batal</button>
                <button type="submit" class="btn-primary" style="background:#dc2626; border-color:#b91c1c;" onclick="return confirm('APAKAH ANDA BENAR-BENAR YAKIN?\nData yang terhapus tidak akan bisa dikembalikan!')">
                    Musnahkan Data
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
<style>
.lap-stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 14px;
    margin-bottom: 20px;
}
.lap-stat-card {
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
.lap-stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.lap-stat-icon svg { width: 20px; height: 20px; }
.lap-stat-val { font-size: 1.5rem; font-weight: 800; color: var(--text-main); line-height: 1.1; }
.lap-stat-label { font-size: 0.78rem; font-weight: 600; color: var(--text-main); margin-top: 2px; }
.lap-stat-sub { font-size: 0.7rem; color: var(--text-muted); margin-top: 1px; }

.lap-main-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 18px;
    align-items: start;
}
@media(max-width: 1100px) { .lap-main-grid { grid-template-columns: 1fr; } }

.badge-purple { background: #ede9fe; color: #7c3aed; border: 1px solid #ddd6fe; }

.export-dropdown {
    display: none;
    position: absolute;
    top: 110%; right: 0;
    background: white;
    border: 1px solid var(--border);
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    min-width: 230px;
    z-index: 50;
    overflow: hidden;
    padding: 6px;
}
.export-dropdown.active { display: block; }
.export-dropdown-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 12px;
    border-radius: 8px;
    font-size: 0.85rem;
    color: var(--text-main);
    text-decoration: none;
    cursor: pointer;
}
.export-dropdown-item svg { width: 17px; height: 17px; flex-shrink: 0; }
.export-dropdown-active:hover { background: #f0fdf4; }

.modal-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(6px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}
.modal-overlay.active {
    display: flex;
}
.modal-box {
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
    overflow: hidden;
    width: 100%;
}
.modal-header {
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
.modal-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    color: #64748b;
}

.foto-thumbnail {
    width: 46px; 
    height: 46px; 
    object-fit: cover; 
    border-radius: 6px; 
    border: 1px solid #cbd5e1;
    cursor: zoom-in;
    transition: all 0.2s ease;
    background: #fff;
}
.foto-thumbnail:hover {
    transform: scale(1.08);
    border-color: #3b82f6;
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    z-index: 10;
}

.custom-pagination-container {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    padding: 18px 24px;
    background: #ffffff;
    border-top: 1px solid #f1f5f9;
    border-radius: 0 0 12px 12px;
    gap: 16px;
}
.custom-pagination-info { font-size: 0.85rem; color: #64748b; }
.custom-pagination-info .font-bold { font-weight: 700; color: #1e293b; }
.custom-pagination-links { display: flex; gap: 8px; align-items: center; }
.c-page-item {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 36px; height: 36px; padding: 0 12px; border-radius: 8px;
    font-size: 0.875rem; font-weight: 600; color: #475569; background: #ffffff;
    border: 1px solid #e2e8f0; text-decoration: none; transition: all 0.2s ease;
}
.c-page-item svg { width: 16px; height: 16px; }
.c-page-item:hover:not(.disabled):not(.active) {
    background: #f8fafc; border-color: #cbd5e1; color: #0f172a; transform: translateY(-1px);
}
.c-page-item.active { background: #2563eb; border-color: #2563eb; color: #ffffff; }
.c-page-item.disabled { background: #f8fafc; color: #cbd5e1; cursor: not-allowed; border-color: #f1f5f9; }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
flatpickr("#dateRangePicker", {
    mode: "range",
    dateFormat: "d M Y",
    defaultDate: ["{{ $tglMulai->format('Y-m-d') }}", "{{ $tglAkhir->format('Y-m-d') }}"],
    onClose: function(selectedDates) {
        if (selectedDates.length === 2) {
            const fmt = d => d.toISOString().split('T')[0];
            document.getElementById('tanggalMulaiInput').value = fmt(selectedDates[0]);
            document.getElementById('tanggalAkhirInput').value = fmt(selectedDates[1]);
        }
    }
});

new Chart(document.getElementById('lapDonutChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['Kondisi Aman', 'Proses Perbaikan', 'Selesai / Ditutup'],
        datasets: [{
            data: [{{ $kondisiAman }}, {{ $prosesPerbaikan }}, {{ $selesaiDitutup }}],
            backgroundColor: ['#16a34a', '#ef4444', '#7c3aed'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '72%',
        plugins: { legend: { display: false } }
    }
});

document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('exportDropdown');
    if (dropdown && !dropdown.contains(e.target) && !e.target.closest('button')) {
        dropdown.classList.remove('active');
    }
});

function printLaporan() {
    const params = new URLSearchParams(window.location.search);
    const url = "{{ route('admin.laporan.pdf') }}?" + params.toString();
    window.open(url, '_blank');
}

function previewImage(url, title) {
    document.getElementById('previewImg').src = url;
    document.getElementById('previewTitle').textContent = title;
    document.getElementById('modalPreviewFoto').classList.add('active');
}

setTimeout(() => {
    const a = document.getElementById('flashAlert');
    if (a) a.style.opacity = '0', setTimeout(() => a.remove(), 400);
    const e = document.getElementById('flashAlertError');
    if (e) e.style.opacity = '0', setTimeout(() => e.remove(), 400);
}, 6000);
</script>
@endpush