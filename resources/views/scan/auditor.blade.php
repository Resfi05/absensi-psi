<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Laporan Auditor - {{ $barang->nama_barang }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --success: #10b981;
            --warning: #f59e0b;
            --dark: #0f172a;
            --gray: #64748b;
            --light-bg: #f8fafc;
            --border: #e2e8f0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        body { 
            background: #f1f5f9; 
            color: var(--dark); 
            -webkit-font-smoothing: antialiased; 
        }

        /* Container ala Mobile App */
        .app-container {
            max-width: 480px; 
            margin: 0 auto;
            background: #ffffff;
            min-height: 100vh;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            position: relative;
            padding-bottom: 40px;
        }

        /* Header Gradient */
        .header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 40px 20px 50px;
            color: white;
            border-bottom-left-radius: 30px;
            border-bottom-right-radius: 30px;
            text-align: center;
            position: relative;
        }
        .header-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 12px;
            backdrop-filter: blur(5px);
            letter-spacing: 0.5px;
        }
        .header-title { font-size: 1.6rem; font-weight: 800; margin-bottom: 8px; line-height: 1.3; }
        .header-subtitle { font-size: 0.9rem; opacity: 0.9; font-weight: 500; display: flex; justify-content: center; align-items: center; gap: 8px; }

        /* Content Wrapper */
        .content { padding: 0 20px; margin-top: -30px; position: relative; z-index: 10; }

        /* --- CSS BARU: FILTER BUTTONS --- */
        .filter-container {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            overflow-x: auto;
            padding-bottom: 8px;
            /* Menyembunyikan scrollbar tapi tetap bisa di-scroll di HP */
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .filter-container::-webkit-scrollbar { display: none; }
        
        .filter-btn {
            background: white;
            border: 1px solid var(--border);
            color: var(--gray);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            transition: all 0.2s ease;
        }
        .filter-btn:hover { background: var(--light-bg); color: var(--dark); }
        .filter-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
        }

        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 20px 16px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            border: 1px solid var(--border);
        }
        .stat-card.aman { border-bottom: 4px solid var(--success); }
        .stat-card.perlu { border-bottom: 4px solid var(--warning); }
        .stat-val { font-size: 2rem; font-weight: 800; margin-bottom: 4px; }
        .stat-val.aman { color: var(--success); }
        .stat-val.perlu { color: var(--warning); }
        .stat-label { font-size: 0.75rem; color: var(--gray); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }

        /* General Card Styling */
        .card {
            background: white;
            border-radius: 20px;
            padding: 24px 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
            border: 1px solid var(--border);
            margin-bottom: 20px;
        }
        .card-title { font-size: 1.1rem; font-weight: 800; color: var(--dark); margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        
        /* Chart Container */
        .chart-wrapper { position: relative; height: 220px; width: 100%; display: flex; justify-content: center; }

        /* Timeline Story */
        .timeline { position: relative; padding-left: 28px; margin-top: 10px; }
        .timeline::before {
            content: ''; position: absolute; left: 9px; top: 5px; bottom: 0; width: 2px; background: #e2e8f0;
        }
        .t-item { position: relative; margin-bottom: 24px; }
        .t-item:last-child { margin-bottom: 0; }
        .t-dot {
            position: absolute; left: -32.5px; top: 4px; width: 14px; height: 14px;
            border-radius: 50%; background: white; border: 3px solid var(--border); z-index: 2;
        }
        .t-dot.aman { border-color: var(--success); }
        .t-dot.perlu { border-color: var(--warning); }
        
        .t-date { font-size: 0.75rem; color: var(--gray); font-weight: 700; display: block; margin-bottom: 8px; }
        .t-box { background: var(--light-bg); border: 1px solid var(--border); border-radius: 14px; padding: 16px; }
        .t-status { font-size: 0.95rem; font-weight: 800; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
        .t-status.aman { color: var(--success); }
        .t-status.perlu { color: var(--warning); }
        .t-user { font-size: 0.8rem; color: var(--gray); font-weight: 500; }
        .t-user b { color: var(--dark); font-weight: 700;}
        .t-note {
            margin-top: 10px; padding-top: 10px; border-top: 1px dashed #cbd5e1;
            font-size: 0.8rem; color: var(--gray); font-style: italic; line-height: 1.5;
        }

        /* Tombol Kembali */
        .btn-back {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; background: white; color: var(--dark); border: 2px solid var(--border);
            padding: 18px; border-radius: 16px; font-size: 0.95rem; font-weight: 700;
            text-decoration: none; margin-top: 10px; transition: all 0.2s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }
        .btn-back:hover { background: var(--light-bg); border-color: #cbd5e1; }
        .btn-back svg { width: 20px; height: 20px; }

        .empty-state { text-align: center; padding: 20px 10px; color: var(--gray); font-size: 0.9rem; font-weight: 500; }
    </style>
</head>
<body>

<div class="app-container">
    
    <div class="header">
        <div class="header-badge">ID: {{ $barang->kode_barang }}</div>
        <h1 class="header-title">{{ $barang->nama_barang }}</h1>
        <div class="header-subtitle">
            <span>📍 {{ $barang->lokasiRelasi->nama ?? '-' }}</span>
            <span>•</span>
            <span>🏷️ {{ $barang->kategori->nama ?? '-' }}</span>
        </div>
    </div>

    <div class="content">

        <!-- --- AREA FILTER BUTTONS --- -->
        <div class="filter-container">
            <a href="?filter=semua" class="filter-btn {{ $filter == 'semua' ? 'active' : '' }}">Semua Waktu</a>
            <a href="?filter=tahun_ini" class="filter-btn {{ $filter == 'tahun_ini' ? 'active' : '' }}">Tahun Ini</a>
            <a href="?filter=bulan_ini" class="filter-btn {{ $filter == 'bulan_ini' ? 'active' : '' }}">Bulan Ini</a>
            <a href="?filter=minggu_ini" class="filter-btn {{ $filter == 'minggu_ini' ? 'active' : '' }}">Minggu Ini</a>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card aman">
                <div class="stat-val aman">{{ $totalAman }}</div>
                <div class="stat-label">Total Aman</div>
            </div>
            <div class="stat-card perlu">
                <div class="stat-val perlu">{{ $totalPerluTindakan }}</div>
                <div class="stat-label">Perlu Tindakan</div>
            </div>
        </div>

        <div class="card">
            <h2 class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:20px; color:var(--primary);"><path d="M21 12v2M6 20v-5M18 20V10M12 20V4"/></svg>
                Rasio Kesehatan Barang
            </h2>
            <div class="chart-wrapper">
                @if($totalPengecekan > 0)
                    <canvas id="healthChart"></canvas>
                @else
                    <div class="empty-state">Belum ada data untuk dibuatkan grafik.</div>
                @endif
            </div>
        </div>

        <div class="card" style="padding-bottom: 10px;">
            <h2 class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:20px; color:var(--primary);"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Riwayat Pengecekan
            </h2>
            
            <div class="timeline">
                @forelse($riwayat as $r)
                    <div class="t-item">
                        <div class="t-dot {{ $r->status == 'aman' ? 'aman' : 'perlu' }}"></div>
                        <span class="t-date">{{ \Carbon\Carbon::parse($r->checked_at)->translatedFormat('l, d F Y - H:i') }}</span>
                        
                        <div class="t-box">
                            <div class="t-status {{ $r->status == 'aman' ? 'aman' : 'perlu' }}">
                                @if($r->status == 'aman')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:16px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    Aman / Normal
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:16px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                    Perlu Tindak Lanjut
                                @endif
                            </div>
                            
                            <div class="t-user">Diperiksa oleh: <b>{{ $r->user->name ?? 'Petugas' }}</b></div>
                            
                            @if($r->keterangan)
                                <div class="t-note">"{{ $r->keterangan }}"</div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        Riwayat masih kosong untuk periode 
                        <b>{{ str_replace('_', ' ', ucwords($filter)) }}</b>.
                    </div>
                @endforelse
            </div>
        </div>

        <a href="/scan/{{ $barang->kode_barang }}" class="btn-back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Kembali ke Halaman Akses
        </a>
        
    </div>
</div>

@if($totalPengecekan > 0)
<script>
    // Inisialisasi Grafik
    const ctx = document.getElementById('healthChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Aman', 'Perlu Tindakan'],
            datasets: [{
                data: [{{ $totalAman }}, {{ $totalPerluTindakan }}],
                backgroundColor: ['#10b981', '#f59e0b'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false, 
            cutout: '70%', 
            plugins: { 
                legend: { 
                    position: 'bottom',
                    labels: {
                        font: { family: 'Plus Jakarta Sans', weight: '600' },
                        padding: 20
                    }
                } 
            } 
        }
    });
</script>
@endif

</body>
</html>