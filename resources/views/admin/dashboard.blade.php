@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- 🌟 WELCOME BANNER 🌟 --}}
<div class="welcome-banner" style="margin-bottom: 20px;">
    <div>
        <h2 class="welcome-title">Selamat Datang, {{ auth()->user()->name }}!</h2>
        <p class="welcome-sub">Berikut adalah ringkasan data inventaris PT Padma Soode Indonesia hari ini.</p>
    </div>
    <div class="welcome-date">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        {{ now()->translatedFormat('l, d F Y') }}
    </div>
</div>

{{-- 🌟 REVISI 2: NOTIFIKASI BARANG EXPIRED / MENDEKATI EXPIRED 🌟 --}}
@if($expired_count > 0)
<div class="alert alert-error" style="background:#fef2f2; border:1px solid #f87171; color:#991b1b; padding:16px 20px; border-radius:12px; margin-bottom:24px; box-shadow: 0 4px 6px rgba(239, 68, 68, 0.1);">
    <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:24px; height:24px;">
            <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <strong style="font-size:1.1rem;">Perhatian! Terdapat {{ $expired_count }} Barang yang akan segera (atau telah) kedaluwarsa:</strong>
    </div>
    <ul style="margin-left:34px; font-size:0.9rem; line-height:1.6; list-style-type: square;">
        @foreach($expired_items as $item)
            @php 
                $tgl = \Carbon\Carbon::parse($item->tanggal_expired);
                $isExpired = $tgl->isPast();
            @endphp
            <li>
                <a href="{{ route('admin.barang.index', ['search' => $item->kode_barang]) }}" style="font-weight:700; color:#991b1b; text-decoration:underline;">{{ $item->nama_barang }} ({{ $item->kode_barang }})</a> 
                — Kategori: <span style="font-weight:600;">{{ $item->kategori->nama ?? 'Tidak Ada' }}</span> 
                — Status: 
                @if($isExpired)
                    <span style="color:#dc2626; font-weight:800; background:#fee2e2; padding:2px 6px; border-radius:4px;">Telah Expired ({{ $tgl->translatedFormat('d M Y') }})</span>
                @else
                    <span style="color:#b45309; font-weight:800; background:#fef3c7; padding:2px 6px; border-radius:4px;">Sisa {{ now()->startOfDay()->diffInDays($tgl) }} Hari ({{ $tgl->translatedFormat('d M Y') }})</span>
                @endif
            </li>
        @endforeach
    </ul>
</div>
@endif

{{-- 🌟 STAT CARDS PER KATEGORI (dinamis) 🌟 --}}
<div class="stats-grid" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">

    {{-- Card Total Semua Barang --}}
    <div class="stat-card" style="border-top:3px solid #1e40af;">
        <div class="stat-icon" style="background:#dbeafe;color:#1e40af;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                <line x1="12" y1="22.08" x2="12" y2="12"></line>
            </svg>
        </div>
        <div class="stat-body">
            <div class="stat-label">Total Semua Barang</div>
            <div class="stat-value">{{ $total_barang }}</div>
            <div class="stat-detail">
                <span class="text-green">Aktif: {{ $total_aktif }}</span>
                <span class="sep">|</span>
                <span class="text-red">Nonaktif: {{ $total_barang - $total_aktif }}</span>
            </div>
        </div>
    </div>

    {{-- Card per Kategori --}}
    @foreach($kategori_stats as $k)
    <div class="stat-card" style="border-top:3px solid {{ $k['warna'] }};">
        <div class="stat-icon" style="background:{{ $k['warna'] }}20;color:{{ $k['warna'] }};">
            @php
                $catName = strtolower($k['nama']);
                $svgIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>'; 
                
                if (str_contains($catName, 'ac') || str_contains($catName, 'pendingin') || str_contains($catName, 'kipas')) {
                    $svgIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.59 4.59A2 2 0 1 1 11 8H2m10.59 11.41A2 2 0 1 0 14 16H2m15.73-8.27A2.5 2.5 0 1 1 19.5 12H2"/></svg>';
                } elseif (str_contains($catName, 'kendaraan') || str_contains($catName, 'mobil') || str_contains($catName, 'motor')) {
                    $svgIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>';
                } elseif (str_contains($catName, 'genset') || str_contains($catName, 'mesin') || str_contains($catName, 'listrik') || str_contains($catName, 'panel')) {
                    $svgIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>';
                } elseif (str_contains($catName, 'komputer') || str_contains($catName, 'it') || str_contains($catName, 'laptop') || str_contains($catName, 'elektronik')) {
                    $svgIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>';
                }
            @endphp
            {!! $svgIcon !!}
        </div>
        <div class="stat-body">
            <div class="stat-label">{{ $k['nama'] }}</div>
            <div class="stat-value">{{ $k['total'] }}</div>
            <div class="stat-detail">
                <span class="text-green">Aktif: {{ $k['aktif'] }}</span>
                <span class="sep">|</span>
                <span class="text-red">Nonaktif: {{ $k['nonaktif'] }}</span>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Card Sudah Dicek --}}
    <div class="stat-card stat-green">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
                <path d="M9 12l2 2 4-4"/>
            </svg>
        </div>
        <div class="stat-body">
            <div class="stat-label">Sudah Dicek</div>
            <div class="stat-value">{{ $selesai }}</div>
            <div class="stat-detail">
                <span class="text-green">Bulan Ini</span>
                <span class="sep">|</span>
                <span class="text-green">{{ $total_jadwal > 0 ? round($selesai / $total_jadwal * 100, 1) : 0 }}%</span>
            </div>
        </div>
    </div>

    {{-- Card Belum Dicek --}}
    <div class="stat-card stat-orange">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <div class="stat-body">
            <div class="stat-label">Belum Dicek</div>
            <div class="stat-value">{{ $pending }}</div>
            <div class="stat-detail">
                <span class="text-orange">Bulan Ini</span>
                <span class="sep">|</span>
                <span class="text-orange">{{ $total_jadwal > 0 ? round($pending / $total_jadwal * 100, 1) : 0 }}%</span>
            </div>
        </div>
    </div>

    {{-- Card Perlu Tindak Lanjut --}}
    <div class="stat-card stat-purple">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
        </div>
        <div class="stat-body">
            <div class="stat-label">Perlu Tindak Lanjut</div>
            <div class="stat-value">{{ $perlu_tindak_lanjut }}</div>
            <div class="stat-detail">
                <span class="text-red">Menunggu Penanganan</span>
                @if($perlu_tindak_lanjut > 0)
                    <span class="sep">|</span>
                    <a href="{{ route('admin.review.index') }}" style="color:#7c3aed;font-weight:600;font-size:0.72rem;">Tinjau ➔</a>
                @endif
            </div>
        </div>
    </div>

    {{-- 🌟 TAMBAHAN STAT CARD EXPIRED 🌟 --}}
    <div class="stat-card stat-red" style="border-top:3px solid #ef4444;">
        <div class="stat-icon" style="background:#fee2e2; color:#ef4444;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
                <line x1="9" y1="16" x2="15" y2="16"/>
            </svg>
        </div>
        <div class="stat-body">
            <div class="stat-label">Barang Mau/Telah Expired</div>
            <div class="stat-value">{{ $expired_count }}</div>
            <div class="stat-detail">
                <span class="text-red">Target < 30 Hari</span>
            </div>
        </div>
    </div>

</div>

{{-- 🌟 MIDDLE ROW: CHART + JADWAL 🌟 --}}
<div class="mid-grid">

    {{-- Chart dinamis semua kategori --}}
    <div class="card chart-card">
        <div class="card-header">
            <h3 class="card-title">Grafik Pengecekan Bulan Ini</h3>
            <span style="font-size:0.78rem;color:var(--text-muted);">per Kategori</span>
        </div>
        <div class="chart-wrap">
            <canvas id="pengecekanChart"></canvas>
        </div>
    </div>

    {{-- Jadwal Hari Ini --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Jadwal Hari Ini</h3>
            <a href="{{ route('admin.jadwal.index') }}" class="card-link">Lihat Semua</a>
        </div>
        <div class="jadwal-list">
            @forelse($jadwal_hari_ini as $j)
                @if($j->barang)
                <div class="jadwal-item">
                    <div class="jadwal-icon"
                         style="background:{{ $j->barang->kategori?->warna ?? '#e2e8f0' }}20;color:{{ $j->barang->kategori?->warna ?? '#64748b' }};">
                        @php
                            $jadwalCatName = strtolower($j->barang->kategori?->nama ?? $j->barang->jenis_barang ?? '');
                            $jadwalSvgIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>'; 
                            
                            if (str_contains($jadwalCatName, 'ac') || str_contains($jadwalCatName, 'pendingin') || str_contains($jadwalCatName, 'kipas')) {
                                $jadwalSvgIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.59 4.59A2 2 0 1 1 11 8H2m10.59 11.41A2 2 0 1 0 14 16H2m15.73-8.27A2.5 2.5 0 1 1 19.5 12H2"/></svg>';
                            } elseif (str_contains($jadwalCatName, 'kendaraan') || str_contains($jadwalCatName, 'mobil') || str_contains($jadwalCatName, 'motor')) {
                                $jadwalSvgIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>';
                            } elseif (str_contains($jadwalCatName, 'genset') || str_contains($jadwalCatName, 'mesin') || str_contains($jadwalCatName, 'listrik') || str_contains($jadwalCatName, 'panel')) {
                                $jadwalSvgIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>';
                            } elseif (str_contains($jadwalCatName, 'komputer') || str_contains($jadwalCatName, 'it') || str_contains($jadwalCatName, 'laptop') || str_contains($jadwalCatName, 'elektronik')) {
                                $jadwalSvgIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>';
                            }
                        @endphp
                        {!! $jadwalSvgIcon !!}
                    </div>
                    <div class="jadwal-info">
                        <div class="jadwal-name">{{ $j->barang->nama_barang }}</div>
                        <div class="jadwal-loc">
                            {{ $j->barang->kategori?->nama ?? $j->barang->jenis_barang }}
                            {{ $j->barang->lokasiRelasi ? '— ' . $j->barang->lokasiRelasi->nama : '' }}
                        </div>
                    </div>
                    <div class="jadwal-right">
                        <span class="jadwal-time">{{ \Carbon\Carbon::parse($j->tanggal_jadwal)->format('d M') }}</span>
                        <span class="badge-status {{ $j->status === 'selesai' ? 'badge-green' : ($j->status === 'terlambat' ? 'badge-red' : 'badge-gray') }}">
                            {{ $j->status === 'selesai' ? 'Selesai' : ($j->status === 'terlambat' ? 'Terlambat' : 'Belum Dicek') }}
                        </span>
                    </div>
                </div>
                @endif
            @empty
            <div class="empty-state">
                <p>Tidak ada jadwal hari ini</p>
            </div>
            @endforelse
        </div>
    </div>

</div>

{{-- 🌟 PENGECEKAN TERBARU 🌟 --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Pengecekan Terbaru</h3>
        <a href="{{ route('admin.monitoring.index') }}" class="card-link">Lihat Semua</a>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Lokasi</th>
                    <th>Petugas</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengecekan_terbaru as $i => $p)
                    @continue(!$p->jadwal || !$p->jadwal->barang)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $p->jadwal->barang->nama_barang }}</td>
                    <td>
                        @if($p->jadwal->barang->kategori)
                            <span class="badge-jenis"
                                  style="background:{{ $p->jadwal->barang->kategori->warna }}20;color:{{ $p->jadwal->barang->kategori->warna }};border:1px solid {{ $p->jadwal->barang->kategori->warna }}40;">
                                {{ $p->jadwal->barang->kategori->nama }}
                            </span>
                        @else
                            <span class="badge-jenis badge-gray">{{ $p->jadwal->barang->jenis_barang }}</span>
                        @endif
                    </td>
                    <td>{{ $p->jadwal->barang->lokasiRelasi->nama ?? '—' }}</td>
                    <td>{{ $p->user->name ?? '—' }}</td>
                    <td>{{ $p->checked_at ? \Carbon\Carbon::parse($p->checked_at)->format('d M Y H:i') : '—' }}</td>
                    <td>
                        @if($p->status === 'aman')
                            <span class="badge-status badge-green">Aman</span>
                        @else
                            <span class="badge-status badge-orange">Perlu Tindakan</span>
                            @if($p->status_tindak_lanjut)
                                <br>
                                <span style="font-size:0.7rem;margin-top:3px;display:inline-block;padding:2px 7px;border-radius:8px;font-weight:600;
                                    background:{{ $p->status_tindak_lanjut === 'selesai' ? '#dcfce7' : ($p->status_tindak_lanjut === 'ditangani' ? '#dbeafe' : ($p->status_tindak_lanjut === 'diabaikan' ? '#f1f5f9' : '#fef3c7')) }};
                                    color:{{ $p->status_tindak_lanjut === 'selesai' ? '#16a34a' : ($p->status_tindak_lanjut === 'ditangani' ? '#2563eb' : ($p->status_tindak_lanjut === 'diabaikan' ? '#64748b' : '#d97706')) }};">
                                    {{ match($p->status_tindak_lanjut) {
                                        'menunggu'  => 'Menunggu',
                                        'ditangani' => 'Ditangani',
                                        'selesai'   => 'Selesai',
                                        'diabaikan' => 'Diabaikan',
                                        default     => $p->status_tindak_lanjut
                                    } }}
                                </span>
                            @endif
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.monitoring.show', $p->id) }}" class="btn-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">Belum ada data pengecekan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
const ctx       = document.getElementById('pengecekanChart').getContext('2d');
const chartData = @json($chart_data);

new Chart(ctx, {
    type: 'line',
    data: {
        labels:   chartData.labels,
        datasets: chartData.datasets,
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                labels: { font: { family: 'Plus Jakarta Sans', size: 12 } }
            }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.05)' } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush