@extends('layouts.user')
@section('title', 'Dashboard')
@section('content')

{{-- Tambahan CSS khusus untuk Swipe Card --}}
<style>
    .swipe-container {
        display: flex;
        overflow-x: auto;
        gap: 16px;
        padding-bottom: 16px;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none; /* Sembunyikan scrollbar di Firefox */
    }
    .swipe-container::-webkit-scrollbar {
        display: none; /* Sembunyikan scrollbar di Chrome/Safari */
    }
    .swipe-card {
        flex: 0 0 92%; /* Kartu mengambil 92% layar, sisanya memperlihatkan ujung kartu selanjutnya */
        scroll-snap-align: center;
    }
</style>

<h1 class="greet-title">Hai, <span class="accent">{{ explode(' ', $user->name)[0] ?? 'Teknisi' }}!</span> 👋</h1>
<p class="greet-sub">Semangat menjalankan pengecekan hari ini!</p>

{{-- ── STAT CARDS ───────────────────────────────── --}}
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--p-primary-lt);color:var(--p-primary);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
            </svg>
        </div>
        <div class="stat-label">Total Pengecekan</div>
        <div class="stat-val">{{ $totalTugas ?? 0 }}</div>
        <div class="stat-unit">Tugas bulan ini</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:var(--p-success-lt);color:var(--p-success);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>
        <div class="stat-label">Selesai</div>
        <div class="stat-val">{{ $selesaiTugas ?? 0 }}</div>
        <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ $persenSelesai ?? 0 }}%;background:var(--p-success);"></div></div>
        <div class="stat-pct" style="color:var(--p-success);">{{ $persenSelesai ?? 0 }}%</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:var(--p-warning-lt);color:var(--p-warning);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <div class="stat-label">Perlu Tindakan</div>
        <div class="stat-val">{{ $perluTindak ?? 0 }}</div>
        <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ $persenPerlu ?? 0 }}%;background:var(--p-warning);"></div></div>
        <div class="stat-pct" style="color:var(--p-warning);">{{ $persenPerlu ?? 0 }}%</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background:var(--p-danger-lt);color:var(--p-danger);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
        </div>
        <div class="stat-label">Terlambat</div>
        <div class="stat-val">{{ $terlambatTugas ?? 0 }}</div>
        <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ $persenLambat ?? 0 }}%;background:var(--p-danger);"></div></div>
        <div class="stat-pct" style="color:var(--p-danger);">{{ $persenLambat ?? 0 }}%</div>
    </div>
</div>

{{-- ── PROGRESS BULAN INI (DONUT) ──────────────── --}}
<div class="progress-card">
    <div class="progress-head">
        <div class="progress-title">Progress Bulan Ini</div>
        <span style="font-size:0.74rem;color:var(--p-muted);font-weight:600;">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
    </div>
    <div class="progress-body">
        <div style="position:relative;width:110px;height:110px;flex-shrink:0;">
            <canvas id="donutChart"></canvas>
            <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;">
                <div style="font-size:1.3rem;font-weight:800;color:var(--p-text);">{{ $totalTugas ?? 0 }}</div>
                <div style="font-size:0.62rem;color:var(--p-muted);">Total</div>
            </div>
        </div>
        <div class="progress-legend">
            <div class="legend-row">
                <span class="legend-left"><span class="legend-dot" style="background:var(--p-success);"></span> Selesai</span>
                <span class="legend-val">{{ $selesaiTugas ?? 0 }} ({{ $persenSelesai ?? 0 }}%)</span>
            </div>
            <div class="legend-row">
                <span class="legend-left"><span class="legend-dot" style="background:var(--p-warning);"></span> Perlu Tindakan</span>
                <span class="legend-val">{{ $perluTindak ?? 0 }} ({{ $persenPerlu ?? 0 }}%)</span>
            </div>
            <div class="legend-row">
                <span class="legend-left"><span class="legend-dot" style="background:var(--p-danger);"></span> Terlambat</span>
                <span class="legend-val">{{ $terlambatTugas ?? 0 }} ({{ $persenLambat ?? 0 }}%)</span>
            </div>
        </div>
    </div>
</div>

{{-- ── JADWAL SELANJUTNYA (BISA DI-SWIPE) ───────── --}}
<div class="section-label" style="margin-top:24px; margin-bottom:12px;">
    Tugas Selanjutnya 
    @if(isset($daftarTugasSelanjutnya) && $daftarTugasSelanjutnya->count() > 1)
        <span style="font-size:0.7rem; color:var(--p-muted); font-weight:normal; margin-left:8px;">(Geser untuk melihat semua)</span>
    @endif
</div>

@if(isset($daftarTugasSelanjutnya) && $daftarTugasSelanjutnya->isNotEmpty())
    
    <div class="swipe-container">
        @foreach($daftarTugasSelanjutnya as $tugas)
            @php
                $tgl = \Carbon\Carbon::parse($tugas->tanggal_jadwal);
                $isToday = $tgl->isToday();
                $belumWaktu = $tgl->copy()->startOfDay()->isFuture();
                
                // Cek prioritas tugas
                $isPerbaikan = $tugas->jenis_jadwal === 'perbaikan'; 
                $isSpesialisasi = $tugas->barang && $tugas->barang->kategori_id == $user->spesialisasi_id;
            @endphp

            <div class="next-task-card swipe-card" @if($isPerbaikan) style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); box-shadow: 0 10px 20px -5px rgba(239, 68, 68, 0.4);" @endif>
                
                <div class="next-task-head">
                    <span class="next-task-label" @if($isPerbaikan) style="color: #fca5a5;" @endif>
                        @if($isPerbaikan)
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            Tindak Lanjut Perbaikan
                        @elseif($isSpesialisasi)
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            Jadwal Pengecekan (Spesialis)
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            Jadwal Pengecekan (Bantuan)
                        @endif
                    </span>
                    <span class="next-task-eta" @if($isPerbaikan) style="background: rgba(255,255,255,0.25); color: white;" @endif>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:11px;height:11px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        {{ $isToday ? 'Hari ini' : $tgl->translatedFormat('d M Y') }}
                    </span>
                </div>
                
                <div class="next-task-body">
                    <div class="next-task-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            @if(($tugas->barang->kategori->nama ?? '') === 'AC')
                                <rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
                            @else
                                <path d="M12 2C8 6 4 9 4 13a8 8 0 0016 0c0-4-4-7-8-11z"/>
                            @endif
                        </svg>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div class="next-task-name">{{ $tugas->barang->nama_barang ?? 'Data barang dihapus' }}</div>
                        <div class="next-task-loc" @if($isPerbaikan) style="color: rgba(255,255,255,0.8);" @endif>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/></svg>
                            {{ $tugas->barang->lokasiRelasi->nama ?? 'Lokasi belum diatur' }}
                        </div>
                    </div>
                </div>
                
                {{-- Logika Tombol --}}
                @if($belumWaktu)
                    <a href="{{ route('user.jadwal.show', $tugas->id) }}" class="next-task-btn" style="background:rgba(255,255,255,0.2);color:white;cursor:not-allowed;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        Belum Waktunya (Terkunci)
                    </a>
                @else
                    <a href="{{ route('user.scan', ['jadwal'=>$tugas->id]) }}" class="next-task-btn" @if($isPerbaikan) style="color: #b91c1c;" @endif>
                        Scan QR & Mulai
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                @endif
            </div>
        @endforeach
    </div>

@else
    {{-- TAMPILAN JIKA JADWAL KOSONG --}}
    <div style="background: white; border: 1px dashed var(--p-muted); border-radius: 12px; padding: 30px 20px; text-align: center;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width: 48px; height: 48px; color: var(--p-muted); margin-bottom: 12px; opacity: 0.5;">
            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        <div style="font-weight: 700; color: var(--p-text); font-size: 1.1rem; margin-bottom: 4px;">Hore! Tidak Ada Tugas Mendesak</div>
        <div style="font-size: 0.85rem; color: var(--p-muted);">Kamu sudah menyelesaikan semua tugas, atau admin belum membuat jadwal baru untukmu.</div>
    </div>
@endif

{{-- ── AKSI CEPAT ───────────────────────────────── --}}
<div class="section-label" style="margin-top:24px;">Aksi Cepat</div>
<div class="quick-grid">
    <a href="{{ route('user.scan') }}" class="quick-item">
        <div class="quick-icon" style="background:var(--p-primary-lt);color:var(--p-primary);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 7V5a2 2 0 012-2h2M17 3h2a2 2 0 012 2v2M21 17v2a2 2 0 01-2 2h-2M7 21H5a2 2 0 01-2-2v-2"/><rect x="7" y="7" width="10" height="10" rx="1"/>
            </svg>
        </div>
        <span class="quick-label">Scan QR</span>
    </a>
    <a href="{{ route('user.jadwal.index', ['tab'=>'hari_ini']) }}" class="quick-item">
        <div class="quick-icon" style="background:var(--p-success-lt);color:var(--p-success);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <span class="quick-label">Tugas Hari Ini</span>
    </a>
    <a href="{{ route('user.riwayat') }}" class="quick-item">
        <div class="quick-icon" style="background:var(--p-warning-lt);color:var(--p-warning);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
            </svg>
        </div>
        <span class="quick-label">Riwayat</span>
    </a>
    <a href="{{ route('user.jadwal.index') }}" class="quick-item">
        <div class="quick-icon" style="background:var(--p-purple-lt);color:var(--p-purple);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
        </div>
        <span class="quick-label">Jadwal Saya</span>
    </a>
</div>

{{-- ── TIP BANNER ───────────────────────────────── --}}
<div class="tip-banner" style="margin-top:24px;">
    <div class="tip-icon">⭐</div>
    <div>
        <div class="tip-title">Jangan lupa cek peralatan dengan teliti</div>
        <div class="tip-sub">Keselamatan dimulai dari pengecekan rutin.</div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('donutChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [{{ $selesaiTugas ?? 0 }}, {{ $perluTindak ?? 0 }}, {{ $terlambatTugas ?? 0 }}],
            backgroundColor: ['#16A34A', '#D97706', '#EF4444'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '70%',
        plugins: { legend: { display: false } }
    }
});
</script>
@endpush