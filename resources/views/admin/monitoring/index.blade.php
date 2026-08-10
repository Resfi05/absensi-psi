@extends('layouts.admin')
@section('title', 'Monitoring Pengecekan')
@section('page-title', 'Monitoring Pengecekan')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-sep">/</span>
            <span class="breadcrumb-current">Monitoring Pengecekan</span>
        </nav>
        <h2 class="page-heading">Monitoring <span class="heading-accent">Pengecekan</span></h2>
    </div>
</div>

{{-- Range Warning --}}
@if($rangeWarning)
<div style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:14px 18px;margin-bottom:20px;display:flex;gap:12px;align-items:flex-start;">
    <svg viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" style="width:20px;height:20px;flex-shrink:0;margin-top:1px;">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    <div style="font-size:0.82rem;color:#92400e;">{{ $rangeWarning }}</div>
</div>
@endif

{{-- Stats Cards --}}
<div class="mon-stats-row">
    <div class="mon-stat-card" style="border-top-color:#2563eb;">
        <div class="mon-stat-icon" style="background:#dbeafe;color:#2563eb;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
        </div>
        <div>
            <div class="mon-stat-val">{{ $totalBarang }}</div>
            <div class="mon-stat-label">Total Barang</div>
            <div class="mon-stat-sub">Periode terpilih</div>
        </div>
    </div>
    <div class="mon-stat-card" style="border-top-color:#16a34a;">
        <div class="mon-stat-icon" style="background:#dcfce7;color:#16a34a;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>
        <div>
            <div class="mon-stat-val">{{ $sudahDicek }}</div>
            <div class="mon-stat-label">Sudah Dicek</div>
            <div class="mon-stat-sub">{{ $totalBarang > 0 ? round($sudahDicek/$totalBarang*100,1) : 0 }}% dari total</div>
        </div>
    </div>
    <div class="mon-stat-card" style="border-top-color:#d97706;">
        <div class="mon-stat-icon" style="background:#fef3c7;color:#d97706;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <div>
            <div class="mon-stat-val">{{ $belumDicek }}</div>
            <div class="mon-stat-label">Belum Dicek</div>
            <div class="mon-stat-sub">{{ $totalBarang > 0 ? round($belumDicek/$totalBarang*100,1) : 0 }}% dari total</div>
        </div>
    </div>
    <div class="mon-stat-card" style="border-top-color:#ef4444;">
        <div class="mon-stat-icon" style="background:#fee2e2;color:#ef4444;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>
        <div>
            <div class="mon-stat-val">{{ $perluTindak }}</div>
            <div class="mon-stat-label">Perlu Tindak Lanjut</div>
            <div class="mon-stat-sub">{{ $totalBarang > 0 ? round($perluTindak/$totalBarang*100,1) : 0 }}% dari total</div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="card" style="margin-bottom:20px;">
    <form method="GET" action="{{ route('admin.monitoring.index') }}" class="jadwal-filter-form" id="filterForm">
        <div style="flex:0 0 230px;">
            <input type="text" id="dateRangePicker" name="date_range" class="filter-input" style="height:40px;width:100%;" placeholder="Pilih rentang tanggal" readonly>
            <input type="hidden" name="tanggal_mulai" id="tanggalMulaiInput" value="{{ $tglMulai->format('Y-m-d') }}">
            <input type="hidden" name="tanggal_akhir" id="tanggalAkhirInput" value="{{ $tglAkhir->format('Y-m-d') }}">
            <span style="font-size:0.68rem;color:var(--text-muted);display:block;margin-top:3px;">Maks. 90 hari per pencarian</span>
        </div>

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

        <select name="status" class="filter-select" onchange="this.form.submit()">
            <option value="semua" {{ !request('status') || request('status')==='semua' ? 'selected':'' }}>Semua Status</option>
            <option value="sudah_dicek"    {{ request('status')==='sudah_dicek'    ? 'selected':'' }}>Sudah Dicek</option>
            <option value="belum_dicek"    {{ request('status')==='belum_dicek'    ? 'selected':'' }}>Belum Dicek</option>
            <option value="perlu_tindakan" {{ request('status')==='perlu_tindakan' ? 'selected':'' }}>Perlu Tindak Lanjut</option>
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
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            Cari
        </button>

        @if(request()->hasAny(['kategori','lokasi','status','search','tanggal_mulai','tanggal_akhir']))
        <a href="{{ route('admin.monitoring.index') }}" class="btn-reset">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>
            </svg>
            Reset Filter
        </a>
        @endif
    </form>
</div>

{{-- Charts Row --}}
<div class="mon-charts-grid">

    {{-- Line Chart --}}
    <div class="card mon-chart-card">
        <div class="card-header">
            <h3 class="card-title">Grafik Pengecekan per Hari</h3>
        </div>
        <div class="chart-wrap" style="height:280px;">
            <canvas id="monLineChart"></canvas>
        </div>
    </div>

    {{-- Donut Chart --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Persentase Status</h3>
        </div>
        <div style="padding:20px;display:flex;flex-direction:column;align-items:center;">
            <div style="position:relative;width:200px;height:200px;">
                <canvas id="monDonutChart"></canvas>
                <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;">
                    <div style="font-size:1.6rem;font-weight:800;color:var(--text-main);">{{ $totalBarang }}</div>
                    <div style="font-size:0.74rem;color:var(--text-muted);">Total</div>
                </div>
            </div>
            <div style="width:100%;margin-top:18px;display:flex;flex-direction:column;gap:10px;">
                <div style="display:flex;align-items:center;justify-content:space-between;font-size:0.8rem;">
                    <span style="display:flex;align-items:center;gap:6px;"><span style="width:9px;height:9px;border-radius:50%;background:#16a34a;"></span> Sudah Dicek</span>
                    <span style="font-weight:700;color:var(--text-main);">{{ $totalBarang > 0 ? round($sudahDicek/$totalBarang*100,1) : 0 }}% ({{ $sudahDicek }})</span>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;font-size:0.8rem;">
                    <span style="display:flex;align-items:center;gap:6px;"><span style="width:9px;height:9px;border-radius:50%;background:#d97706;"></span> Belum Dicek</span>
                    <span style="font-weight:700;color:var(--text-main);">{{ $totalBarang > 0 ? round($belumDicek/$totalBarang*100,1) : 0 }}% ({{ $belumDicek }})</span>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;font-size:0.8rem;">
                    <span style="display:flex;align-items:center;gap:6px;"><span style="width:9px;height:9px;border-radius:50%;background:#ef4444;"></span> Perlu Tindak Lanjut</span>
                    <span style="font-weight:700;color:var(--text-main);">{{ $totalBarang > 0 ? round($perluTindak/$totalBarang*100,1) : 0 }}% ({{ $perluTindak }})</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Ringkasan Status --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ringkasan Status</h3>
        </div>
        <div style="padding:8px 0;">
            <div class="mon-ringkasan-item">
                <span class="mon-ringkasan-dot" style="background:#dcfce7;color:#16a34a;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </span>
                <span class="mon-ringkasan-label">Sudah Dicek</span>
                <span class="mon-ringkasan-val">{{ $sudahDicek }}</span>
            </div>
            <div class="mon-ringkasan-item">
                <span class="mon-ringkasan-dot" style="background:#fef3c7;color:#d97706;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </span>
                <span class="mon-ringkasan-label">Belum Dicek</span>
                <span class="mon-ringkasan-val">{{ $belumDicek }}</span>
            </div>
            <div class="mon-ringkasan-item">
                <span class="mon-ringkasan-dot" style="background:#fee2e2;color:#ef4444;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </span>
                <span class="mon-ringkasan-label">Perlu Tindak Lanjut</span>
                <span class="mon-ringkasan-val">{{ $perluTindak }}</span>
            </div>
            <div class="mon-ringkasan-total">
                <span>Total</span>
                <span>{{ $totalBarang }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Main: Table + Sidebar terbaru --}}
<div class="mon-main-grid">

    {{-- Tabel --}}
    <div class="card">
        <div class="card-header">
            <div class="total-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                </svg>
                Daftar Pengecekan &nbsp;<strong>{{ $pengecekan->total() }}</strong>
            </div>
            <a href="{{ request()->fullUrlWithQuery(['export'=>'excel']) }}" class="btn-export">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                Export Excel
            </a>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Jenis</th>
                        <th>Lokasi</th>
                        <th>Jadwal</th>
                        <th>Status</th>
                        <th>Terakhir Dicek</th>
                        <th>Oleh</th>
                        <th style="text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengecekan as $i => $row)
                    @php
                        $jadwal = $row['jadwal'];
                        $p    = $row['pengecekan'];
                        $status = $row['status_real'];
                        $barang = $jadwal->barang;
                        $statusInfo = match($status) {
                            'sudah_dicek'    => ['label'=>'Sudah Dicek', 'badge'=>'badge-green'],
                            'perlu_tindakan' => ['label'=>'Perlu Tindak Lanjut', 'badge'=>'badge-red'],
                            default          => ['label'=>'Belum Dicek', 'badge'=>'badge-orange'],
                        };
                    @endphp
                    <tr>
                        <td class="text-muted text-sm">{{ $pengecekan->firstItem() + $i }}</td>
                        <td><span class="kode-barang">{{ $barang->kode_barang ?? '???' }}</span></td>
                        <td style="font-weight:600;font-size:0.85rem;color:var(--text-main);">{{ $barang->nama_barang ?? '???' }}</td>
                        <td>
                            @if($barang->kategori ?? null)
                                <span class="badge-jenis" style="background:{{ $barang->kategori->warna }}20;color:{{ $barang->kategori->warna }};border:1px solid {{ $barang->kategori->warna }}40;">
                                    {{ $barang->kategori->nama }}
                                </span>
                            @else
                                <span class="text-muted text-sm">—</span>
                            @endif
                        </td>
                        <td style="font-size:0.8rem;color:var(--text-muted);">{{ $barang->lokasiRelasi->nama ?? '???' }}</td>
                        <td style="font-size:0.8rem;color:var(--text-muted);">{{ \Carbon\Carbon::parse($jadwal->tanggal_jadwal)->translatedFormat('d M Y') }}</td>
                        <td>
                            <span class="badge-status {{ $statusInfo['badge'] }}">{{ $statusInfo['label'] }}</span>
                            @if($status === 'perlu_tindakan' && $p && $p->status_tindak_lanjut)
                                <div style="margin-top:4px;">
                                    <span class="badge-status" style="background:{{ $p->statusTindakLanjutColor() }}15;color:{{ $p->statusTindakLanjutColor() }};border:1px solid {{ $p->statusTindakLanjutColor() }}35;font-size:0.64rem;padding:2px 6px;">
                                        {{ $p->statusTindakLanjutLabel() }}
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td style="font-size:0.78rem;color:var(--text-muted);">
                            {{ $p && $p->checked_at ? \Carbon\Carbon::parse($p->checked_at)->translatedFormat('d M Y H:i') : '???' }}
                        </td>
                        <td style="font-size:0.8rem;color:var(--text-muted);">{{ $p->user->name ?? '???' }}</td>
                        <td>
                            <div class="aksi-group" style="justify-content:center;">
                                @if($p)
                                <a href="{{ route('admin.monitoring.show', $p->id) }}" class="btn-aksi btn-aksi-view" title="Detail">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </a>
                                @else
                                <span class="btn-aksi" style="background:#f1f5f9;color:#cbd5e1;cursor:not-allowed;" title="Belum ada pengecekan">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10">
                            <div class="empty-table-state" style="padding:40px; text-align:center; color:#94a3b8;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:48px; height:48px; margin:0 auto 10px;">
                                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                                </svg>
                                <p>Tidak ada data untuk filter ini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 📌 PAGINASI PREMIUM 📌 --}}
        @if($pengecekan->hasPages())
        <div class="custom-pagination-container">
            <div class="custom-pagination-info">
                Menampilkan <span class="font-bold">{{ $pengecekan->firstItem() }}</span> - <span class="font-bold">{{ $pengecekan->lastItem() }}</span> dari <span class="font-bold">{{ $pengecekan->total() }}</span> data
            </div>
            
            <div class="custom-pagination-links">
                @if($pengecekan->onFirstPage())
                    <span class="c-page-item disabled">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </span>
                @else
                    <a href="{{ $pengecekan->previousPageUrl() }}" class="c-page-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </a>
                @endif
                
                @foreach($pengecekan->getUrlRange(max(1, $pengecekan->currentPage() - 2), min($pengecekan->lastPage(), $pengecekan->currentPage() + 2)) as $page => $url)
                    @if($page == $pengecekan->currentPage())
                        <span class="c-page-item active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="c-page-item">{{ $page }}</a>
                    @endif
                @endforeach
                
                @if($pengecekan->hasMorePages())
                    <a href="{{ $pengecekan->nextPageUrl() }}" class="c-page-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                @else
                    <span class="c-page-item disabled">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </span>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar: Pengecekan Terbaru --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Pengecekan Terbaru</h3>
        </div>
        <div style="padding:8px 12px;">
            @forelse($terbaru as $t)
            <a href="{{ route('admin.monitoring.show', $t->id) }}" class="mon-terbaru-item">
                
                {{-- 🔥 REVISI: FOTO BARANG ATAU KUBUS 3D DENGAN WARNA KATEGORI ASLI 🔥 --}}
                <div class="recent-check-logo">
                    @php
                        $fotoLogo = null;
                        if (!empty($t->foto)) {
                            $fotoLogo = str_starts_with($t->foto, 'http') ? $t->foto : asset('storage/' . $t->foto);
                        } elseif (!empty($t->foto_sesudah)) {
                            $fotoLogo = str_starts_with($t->foto_sesudah, 'http') ? $t->foto_sesudah : asset('storage/' . $t->foto_sesudah);
                        } elseif (!empty($t->jadwal->barang->foto)) {
                            $fotoLogo = str_starts_with($t->jadwal->barang->foto, 'http') ? $t->jadwal->barang->foto : asset('storage/' . $t->jadwal->barang->foto);
                        }
                        
                        $catColor = $t->jadwal->barang->kategori->warna ?? '#2563eb';
                    @endphp

                    @if($fotoLogo)
                        <img src="{{ $fotoLogo }}" alt="Logo" loading="lazy">
                    @else
                        <div class="logo-fallback" style="background:{{ $catColor }}20; color:{{ $catColor }}; border:1px solid {{ $catColor }}40; width:100%; height:100%; display:flex; align-items:center; justify-content:center; border-radius:8px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px; height:20px;">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                <line x1="12" y1="22.08" x2="12" y2="12"></line>
                            </svg>
                        </div>
                    @endif
                </div>

                <div style="flex:1;min-width:0;">
                    <div class="mon-terbaru-nama">{{ $t->jadwal->barang->nama_barang ?? '???' }}</div>
                    <div class="mon-terbaru-sub">Oleh: {{ $t->user->name ?? '???' }}</div>
                </div>
                <div style="text-align:right;">
                    @php
                        $badgeInfo = match($t->status) {
                            'aman' => ['Sudah Dicek','badge-green'],
                            'perlu_tindakan' => ['Perlu Tindak Lanjut','badge-red'],
                            default => ['Tertunda','badge-orange'],
                        };
                    @endphp
                    <span class="badge-status {{ $badgeInfo[1] }}" style="font-size:0.66rem;">{{ $badgeInfo[0] }}</span>
                    <div style="font-size:0.68rem;color:var(--text-muted);margin-top:3px;">
                        {{ $t->checked_at ? \Carbon\Carbon::parse($t->checked_at)->translatedFormat('d M H:i') : '' }}
                    </div>
                </div>
            </a>
            @empty
            <div style="text-align:center;padding:30px 10px;color:var(--text-muted);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:36px;height:36px;opacity:0.3;margin-bottom:8px;">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                </svg>
                <p style="font-size:0.8rem;">Belum ada aktivitas pengecekan</p>
            </div>
            @endforelse
        </div>
        <div style="padding:0 16px 16px;">
            <a href="{{ route('admin.monitoring.index', ['tanggal_mulai'=>$tglMulai->format('Y-m-d'),'tanggal_akhir'=>$tglAkhir->format('Y-m-d')]) }}"
               class="btn-secondary btn-full" style="justify-content:center;">
                Lihat Semua Aktivitas
            </a>
        </div>
    </div>

</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
<style>
.mon-stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 14px;
    margin-bottom: 20px;
}
.mon-stat-card {
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
.mon-stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.mon-stat-icon svg { width: 20px; height: 20px; }
.mon-stat-val { font-size: 1.5rem; font-weight: 800; color: var(--text-main); line-height: 1.1; }
.mon-stat-label { font-size: 0.78rem; font-weight: 600; color: var(--text-main); margin-top: 2px; }
.mon-stat-sub { font-size: 0.7rem; color: var(--text-muted); margin-top: 1px; }

.mon-charts-grid {
    display: grid;
    grid-template-columns: 1.6fr 1fr 1fr;
    gap: 18px;
    margin-bottom: 20px;
    align-items: stretch;
}
@media(max-width: 1200px) { .mon-charts-grid { grid-template-columns: 1fr; } }

.mon-ringkasan-item {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 18px;
    border-bottom: 1px solid var(--border);
}
.mon-ringkasan-dot {
    width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.mon-ringkasan-dot svg { width: 16px; height: 16px; }
.mon-ringkasan-label { flex: 1; font-size: 0.85rem; color: var(--text-main); font-weight: 500; }
.mon-ringkasan-val { font-size: 0.95rem; font-weight: 700; color: var(--text-main); }
.mon-ringkasan-total {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 18px;
    font-size: 0.85rem; font-weight: 700; color: var(--text-main);
}

.mon-main-grid {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 18px;
    align-items: start;
}
@media(max-width: 1100px) { .mon-main-grid { grid-template-columns: 1fr; } }

/* 🌟 CSS UNTUK LOGO PENGECEKAN TERBARU 🌟 */
.mon-terbaru-item {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 10px;
    border-radius: 8px;
    text-decoration: none;
    transition: background 0.15s;
    border-bottom: 1px dashed #f1f5f9;
}
.mon-terbaru-item:hover { background: #f8fafc; }
.mon-terbaru-nama { font-size: 0.82rem; font-weight: 700; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.mon-terbaru-sub { font-size: 0.72rem; color: #64748b; margin-top: 2px; }

.recent-check-logo {
    flex-shrink: 0;
    width: 44px; 
    height: 44px;
    border-radius: 8px;
    overflow: hidden;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    justify-content: center;
}
.recent-check-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}
.logo-fallback {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.logo-fallback svg {
    width: 20px;
    height: 20px;
}

/* 🎨 VARIASI PAGINASI PREMIUM 🎨 */
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
.custom-pagination-info {
    font-size: 0.85rem;
    color: #64748b;
}
.custom-pagination-info .font-bold {
    font-weight: 700;
    color: #1e293b;
}
.custom-pagination-links {
    display: flex;
    gap: 8px;
    align-items: center;
}
.c-page-item {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 12px;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 600;
    color: #475569;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    text-decoration: none;
    transition: all 0.25s ease;
    cursor: pointer;
}
.c-page-item svg { width: 16px; height: 16px; }
.c-page-item:hover:not(.disabled):not(.active) {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #0f172a;
    box-shadow: 0 2px 4px rgba(0,0,0,0.04);
    transform: translateY(-1px);
}
.c-page-item.active {
    background: #2563eb;
    border-color: #2563eb;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
}
.c-page-item.disabled {
    background: #f8fafc;
    color: #cbd5e1;
    cursor: not-allowed;
    border-color: #f1f5f9;
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
// Date Range Picker
flatpickr("#dateRangePicker", {
    mode: "range",
    dateFormat: "d M Y",
    defaultDate: ["{{ $tglMulai->format('Y-m-d') }}", "{{ $tglAkhir->format('Y-m-d') }}"],
    onClose: function(selectedDates) {
        if (selectedDates.length === 2) {
            const fmt = d => d.toISOString().split('T')[0];
            document.getElementById('tanggalMulaiInput').value = fmt(selectedDates[0]);
            document.getElementById('tanggalAkhirInput').value = fmt(selectedDates[1]);
            document.getElementById('filterForm').submit();
        }
    }
});

// Line Chart
const chartData = @json($chart_data);
new Chart(document.getElementById('monLineChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: chartData.labels,
        datasets: [
            {
                label: 'Sudah Dicek',
                data: chartData.sudah,
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22,163,74,.08)',
                borderWidth: 2.5,
                pointRadius: 3,
                tension: 0.4,
                fill: true,
            },
            {
                label: 'Belum Dicek',
                data: chartData.belum,
                borderColor: '#d97706',
                backgroundColor: 'rgba(217,119,6,.08)',
                borderWidth: 2.5,
                pointRadius: 3,
                tension: 0.4,
                fill: true,
                borderDash: [5,4],
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'top', labels: { font: { family: 'Plus Jakarta Sans', size: 12 } } } },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.05)' } },
            x: { grid: { display: false } }
        }
    }
});

// Donut Chart
new Chart(document.getElementById('monDonutChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['Sudah Dicek', 'Belum Dicek', 'Perlu Tindak Lanjut'],
        datasets: [{
            data: [{{ $sudahDicek }}, {{ $belumDicek }}, {{ $perluTindak }}],
            backgroundColor: ['#16a34a', '#d97706', '#ef4444'],
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
</script>
@endpush