@extends('layouts.admin')
@section('title', 'Log Aktivitas')
@section('page-title', 'Log Aktivitas')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-current">Log Aktivitas</span>
        </nav>
        <h2 class="page-heading">Log <span class="heading-accent">Aktivitas</span></h2>
        <p style="font-size:0.82rem;color:var(--text-muted);margin-top:4px;">Jejak audit seluruh aktivitas admin di sistem</p>
    </div>
</div>

{{-- Stats --}}
<div class="log-stats-row">
    <div class="log-stat-card" style="border-top-color:#2563eb;">
        <div class="log-stat-icon" style="background:#dbeafe;color:#2563eb;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M12 2v2M12 20v2M20 12h2M2 12h2M19.07 19.07l-1.41-1.41M4.93 19.07l1.41-1.41"/>
            </svg>
        </div>
        <div>
            <div class="log-stat-val">{{ $totalHariIni }}</div>
            <div class="log-stat-label">Aktivitas Hari Ini</div>
        </div>
    </div>
    <div class="log-stat-card" style="border-top-color:#7c3aed;">
        <div class="log-stat-icon" style="background:#ede9fe;color:#7c3aed;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
        </div>
        <div>
            <div class="log-stat-val">{{ $totalMingguIni }}</div>
            <div class="log-stat-label">Minggu Ini</div>
        </div>
    </div>
    <div class="log-stat-card" style="border-top-color:#10b981;">
        <div class="log-stat-icon" style="background:#d1fae5;color:#10b981;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
        </div>
        <div>
            <div class="log-stat-val">{{ $totalBulanIni }}</div>
            <div class="log-stat-label">Bulan Ini</div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="card" style="margin-bottom:20px;">
    <form method="GET" action="{{ route('admin.log-aktivitas.index') }}" class="jadwal-filter-form">
        <select name="aksi" class="filter-select" onchange="this.form.submit()">
            <option value="semua" {{ !request('aksi') || request('aksi')==='semua' ? 'selected':'' }}>Semua Aksi</option>
            <option value="created"       {{ request('aksi')==='created'       ? 'selected':'' }}>Menambahkan</option>
            <option value="updated"       {{ request('aksi')==='updated'       ? 'selected':'' }}>Mengubah</option>
            <option value="deleted"       {{ request('aksi')==='deleted'       ? 'selected':'' }}>Menghapus</option>
            <option value="tindak_lanjut" {{ request('aksi')==='tindak_lanjut' ? 'selected':'' }}>Tindak Lanjut</option>
            <option value="login"         {{ request('aksi')==='login'         ? 'selected':'' }}>Login</option>
            <option value="logout"        {{ request('aksi')==='logout'        ? 'selected':'' }}>Logout</option>
            <option value="pengecekan"    {{ request('aksi')==='pengecekan'    ? 'selected':'' }}>Pengecekan</option>
        </select>

        <select name="user_id" class="filter-select" onchange="this.form.submit()">
            <option value="">Semua User</option>
            @foreach($userList as $u)
                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected':'' }}>{{ $u->name }}</option>
            @endforeach
        </select>

        <select name="rentang_waktu" class="filter-select" onchange="this.form.submit()">
            <option value="semua" {{ !request('rentang_waktu') || request('rentang_waktu')==='semua' ? 'selected':'' }}>Semua Waktu</option>
            <option value="hari_ini" {{ request('rentang_waktu')==='hari_ini' ? 'selected':'' }}>Hari Ini</option>
            <option value="minggu_ini" {{ request('rentang_waktu')==='minggu_ini' ? 'selected':'' }}>Minggu Ini</option>
            <option value="bulan_ini" {{ request('rentang_waktu')==='bulan_ini' ? 'selected':'' }}>Bulan Ini</option>
        </select>

        <div class="search-input-wrap" style="flex:1;min-width:200px;">
            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" name="search" placeholder="Cari deskripsi aktivitas..."
                   value="{{ request('search') }}" class="filter-input search-field">
        </div>

        <button type="submit" class="btn-filter">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            Cari
        </button>

        @if(request()->hasAny(['aksi','user_id','search','rentang_waktu']))
        <a href="{{ route('admin.log-aktivitas.index') }}" class="btn-reset">Reset</a>
        @endif
    </form>
</div>

{{-- Timeline Activity --}}
<div class="card">
    <div class="card-header">
        <div class="total-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"/>
            </svg>
            Riwayat Aktivitas &nbsp;<strong>{{ $logs->total() }}</strong>
        </div>
        <span style="font-size:0.74rem;color:var(--text-muted);">
            @if(request('rentang_waktu') === 'hari_ini')
                Menampilkan data hari ini
            @elseif(request('rentang_waktu') === 'minggu_ini')
                Menampilkan data minggu ini
            @elseif(request('rentang_waktu') === 'bulan_ini')
                Menampilkan data bulan ini
            @else
                Default menampilkan data terbaru
            @endif
        </span>
    </div>

    <div class="log-timeline">
        @forelse($logs as $log)
        <div class="log-item">
            <div class="log-item-icon" style="background:{{ $log->aksiColor() }}15;color:{{ $log->aksiColor() }};">
                @if($log->aksi === 'created' || $log->aksi === 'create')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                @elseif($log->aksi === 'updated' || $log->aksi === 'update')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                @elseif($log->aksi === 'deleted' || $log->aksi === 'delete')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                @elseif($log->aksi === 'tindak_lanjut')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                @elseif($log->aksi === 'login')
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                @else
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                @endif
            </div>

            <div class="log-item-body">
                <div class="log-item-top">
                    <div class="log-item-avatar">{{ strtoupper(substr($log->user->name ?? 'S',0,1)) }}</div>
                    <span class="log-item-desc">{{ $log->deskripsi }}</span>
                </div>
                <div class="log-item-meta">
                    <span class="badge-status" style="background:{{ $log->aksiColor() }}15;color:{{ $log->aksiColor() }};border:1px solid {{ $log->aksiColor() }}35;font-size:0.66rem;">
                        {{ $log->aksiLabel() }}
                    </span>
                    @if($log->model_type)
                        <span class="log-item-model">{{ $log->modelLabel() }}</span>
                    @endif
                    <span class="log-item-time">{{ $log->created_at->translatedFormat('d M Y, H:i') }}</span>
                    <span class="log-item-time" style="color:#cbd5e1;">({{ $log->created_at->diffForHumans() }})</span>
                </div>

                @if(($log->aksi === 'updated' || $log->aksi === 'update') && $log->data_lama && $log->data_baru)
                <div class="log-item-diff">
                    @foreach($log->data_baru as $field => $newVal)
                        @if($field !== 'password' && isset($log->data_lama[$field]))
                        <div class="log-diff-row">
                            <span class="log-diff-field">{{ str_replace('_',' ',ucfirst($field)) }}:</span>
                            <span class="log-diff-old">{{ is_array($log->data_lama[$field]) ? json_encode($log->data_lama[$field]) : $log->data_lama[$field] }}</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:11px;height:11px;color:#94a3b8;"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                            <span class="log-diff-new">{{ is_array($newVal) ? json_encode($newVal) : $newVal }}</span>
                        </div>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="empty-table-state" style="padding:50px 20px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M12 2v2M12 20v2M20 12h2M2 12h2M19.07 19.07l-1.41-1.41M4.93 19.07l1.41-1.41"/>
            </svg>
            <p>Belum ada aktivitas tercatat untuk filter ini</p>
        </div>
        @endforelse
    </div>

    @if($logs->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Menampilkan {{ $logs->firstItem() }} sampai {{ $logs->lastItem() }} dari {{ $logs->total() }} data
        </div>
        <div class="pagination-links">
            @if($logs->onFirstPage())
                <span class="page-btn page-btn-disabled">‹</span>
            @else
                <a href="{{ $logs->previousPageUrl() }}" class="page-btn">‹</a>
            @endif
            @foreach($logs->getUrlRange(max(1,$logs->currentPage()-2),min($logs->lastPage(),$logs->currentPage()+2)) as $page => $url)
                @if($page == $logs->currentPage())
                    <span class="page-btn page-btn-active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                @endif
            @endforeach
            @if($logs->hasMorePages())
                <a href="{{ $logs->nextPageUrl() }}" class="page-btn">›</a>
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
.log-stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 14px;
    margin-bottom: 20px;
    max-width: 690px;
}
.log-stat-card {
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
.log-stat-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.log-stat-icon svg { width: 20px; height: 20px; }
.log-stat-val { font-size: 1.4rem; font-weight: 800; color: var(--text-main); line-height: 1.1; }
.log-stat-label { font-size: 0.76rem; color: var(--text-muted); margin-top: 2px; }

.log-timeline { padding: 8px 20px; }
.log-item {
    display: flex;
    gap: 14px;
    padding: 16px 0;
    border-bottom: 1px solid var(--border);
}
.log-item:last-child { border-bottom: none; }
.log-item-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.log-item-icon svg { width: 17px; height: 17px; }
.log-item-body { flex: 1; min-width: 0; }
.log-item-top { display: flex; align-items: flex-start; gap: 8px; margin-bottom: 6px; }
.log-item-avatar {
    width: 22px; height: 22px;
    border-radius: 50%;
    background: linear-gradient(135deg,#1e40af,#3b82f6);
    color: white;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.62rem; font-weight: 700;
    flex-shrink: 0;
    margin-top: 1px;
}
.log-item-desc { font-size: 0.85rem; color: var(--text-main); line-height: 1.5; }
.log-item-meta {
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    margin-left: 30px;
}
.log-item-model {
    font-size: 0.68rem;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    padding: 2px 7px;
    border-radius: 6px;
}
.log-item-time { font-size: 0.7rem; color: #94a3b8; }

.log-item-diff {
    margin-left: 30px;
    margin-top: 8px;
    background: #f8fafc;
    border-radius: 8px;
    padding: 8px 12px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.log-diff-row {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.74rem;
    flex-wrap: wrap;
}
.log-diff-field { font-weight: 600; color: #475569; min-width: 80px; }
.log-diff-old { color: #ef4444; text-decoration: line-through; }
.log-diff-new { color: #16a34a; font-weight: 600; }
</style>
@endpush