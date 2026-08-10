<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — PT Padma Soode Indonesia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/qrcode.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jadwal.css') }}">
    @stack('styles')
</head>
<body>

<!-- ═══════════════════════════════════════
     SIDEBAR
════════════════════════════════════════ -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="sidebar-logo">
        <div class="sidebar-brand">
            <span class="brand-name">PT PADMA SOODE</span>
            <span class="brand-sub">INDONESIA</span>
        </div>
        <button class="sidebar-close" onclick="toggleSidebar()">✕</button>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">MASTER DATA</div>

        <a href="{{ route('admin.users.index') }}"
           class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
                </svg>
            </span>
            <span>Kelola User</span>
        </a>

        <div class="nav-item-group {{ request()->routeIs('admin.barang*') || request()->routeIs('admin.kategori*') || request()->routeIs('admin.lokasi*') || request()->routeIs('admin.waktu*') ? 'open' : '' }}">
            <div class="nav-item nav-item-parent {{ request()->routeIs('admin.barang*') || request()->routeIs('admin.kategori*') || request()->routeIs('admin.lokasi*') || request()->routeIs('admin.waktu*') ? 'active' : '' }}"
                 onclick="toggleSubmenu(this)">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/>
                        <line x1="12" y1="17" x2="12" y2="21"/>
                    </svg>
                </span>
                <span>Kelola Data Barang</span>
                <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </div>
            <div class="nav-submenu">
                <a href="{{ route('admin.barang.index') }}" class="nav-sub-item {{ request()->routeIs('admin.barang*') ? 'active' : '' }}">
                    Data Barang
                </a>
                <a href="{{ route('admin.kategori.index') }}" class="nav-sub-item {{ request()->routeIs('admin.kategori*') ? 'active' : '' }}">
                    Kategori Barang
                </a>
                <a href="{{ route('admin.lokasi.index') }}" class="nav-sub-item {{ request()->routeIs('admin.lokasi*') ? 'active' : '' }}">
                    Lokasi Barang
                </a>
                <a href="{{ route('admin.waktu.index') }}" class="nav-sub-item {{ request()->routeIs('admin.waktu*') ? 'active' : '' }}">
                    Manajemen Waktu
                </a>
            </div>
        </div>

        <div class="nav-section-label">PENGELOLAAN</div>

        <a href="{{ route('admin.qrcode.index') }}"
           class="nav-item {{ request()->routeIs('admin.qrcode*') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="3" height="3"/>
                    <line x1="20" y1="14" x2="20" y2="14"/><line x1="20" y1="20" x2="20" y2="20"/>
                    <line x1="14" y1="20" x2="14" y2="20"/>
                </svg>
            </span>
            <span>Generate QR Code</span>
        </a>

        <a href="{{ route('admin.jadwal.index') }}"
           class="nav-item {{ request()->routeIs('admin.jadwal*') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </span>
            <span>Manajemen Jadwal</span>
        </a>

        <div class="nav-section-label">MONITORING</div>

        <a href="{{ route('admin.monitoring.index') }}"
           class="nav-item {{ request()->routeIs('admin.monitoring*') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                </svg>
            </span>
            <span>Monitoring Pengecekan</span>
        </a>

        <a href="{{ route('admin.review.index') }}" class="nav-item {{ request()->routeIs('admin.review*') ? 'active' : '' }}" style="position:relative;">
    <span class="nav-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
            <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
        </svg>
    </span>
    <span>Review Hasil</span>
    @if(($menungguReviewCount ?? 0) > 0)
        <span style="margin-left:auto;background:#ef4444;color:white;font-size:0.68rem;font-weight:700;padding:2px 7px;border-radius:10px;min-width:20px;text-align:center;line-height:1.3;">
            {{ $menungguReviewCount }}
        </span>
    @endif
</a>

        <div class="nav-section-label">LAPORAN</div>

        <a href="{{ route('admin.laporan.index') }}" class="nav-item {{ request()->routeIs('admin.laporan*') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/>
                </svg>
            </span>
            <span>Laporan & Export</span>
        </a>
    </nav>

</aside>

<!-- OVERLAY -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- ═══════════════════════════════════════
     MAIN CONTENT
════════════════════════════════════════ -->
<div class="main-wrapper" id="mainWrapper">

    <!-- TOPBAR -->
    <header class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" onclick="toggleSidebar()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
            <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
        </div>
        <div class="topbar-right">

            {{-- ── NOTIFICATION BELL DROPDOWN ─────────────── --}}
            <div class="notif-menu-wrap" id="notifMenuWrap">
                <button class="topbar-btn notif-btn" onclick="toggleNotifDropdown(event)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 01-3.46 0"/>
                    </svg>
                    @if(($notifikasiCount ?? 0) > 0)
                        <span class="notif-badge">{{ $notifikasiCount }}</span>
                    @endif
                </button>

                <div class="notif-dropdown" id="notifDropdown">
                    <div class="notif-dropdown-header">
                        <span>Notifikasi</span>
                        @if(($notifikasiCount ?? 0) > 0)
                            <span class="notif-dropdown-count">{{ $notifikasiCount }} baru</span>
                        @endif
                    </div>

                    <div class="notif-dropdown-body">
                        @forelse(($notifikasiList ?? []) as $n)
                        <a href="{{ $n['url'] }}" class="notif-item">
                            <span class="notif-item-dot" style="background:{{ $n['warna'] }};"></span>
                            <div class="notif-item-text">
                                <div class="notif-item-title">{{ $n['judul'] }}</div>
                                <div class="notif-item-sub">{{ $n['sub'] }}</div>
                                <div class="notif-item-time">
                                    {{ $n['waktu'] ? \Carbon\Carbon::parse($n['waktu'])->diffForHumans() : '' }}
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="notif-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:32px;height:32px;opacity:0.5;margin-bottom:6px;">
                                <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            <p>Tidak ada notifikasi baru</p>
                        </div>
                        @endforelse
                    </div>

                    @if(($notifikasiCount ?? 0) > 0)
                    <div class="notif-dropdown-footer">
                        <a href="{{ route('admin.review.index') }}">Lihat semua di Review Hasil →</a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ── USER DROPDOWN ──────────────────────────── --}}
            <div class="user-menu-wrap" id="userMenuWrap">
                <div class="user-menu" onclick="toggleUserDropdown(event)">
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="user-info">
                        <span class="user-name">{{ auth()->user()->name }}</span>
                        <span class="user-role">{{ auth()->user()->role === 'admin' ? 'Super Admin' : 'Petugas' }}</span>
                    </div>
                    <svg class="user-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;color:#94a3b8">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </div>

                <div class="user-dropdown" id="userDropdown">
                    <div class="user-dropdown-header">
                        <div class="user-avatar" style="width:38px;height:38px;font-size:0.95rem;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="user-dropdown-name">{{ auth()->user()->name }}</div>
                            <div class="user-dropdown-email">{{ auth()->user()->email }}</div>
                        </div>
                    </div>

                    <div class="user-dropdown-divider"></div>

                    <a href="{{ route('admin.pengaturan.index') }}" class="user-dropdown-item {{ request()->routeIs('admin.pengaturan*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                        </svg>
                        <span>Pengaturan Akun</span>
                    </a>

                    <a href="{{ route('admin.log-aktivitas.index') }}" class="user-dropdown-item {{ request()->routeIs('admin.log-aktivitas*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M12 2v2M12 20v2M20 12h2M2 12h2M19.07 19.07l-1.41-1.41M4.93 19.07l1.41-1.41"/>
                        </svg>
                        <span>Log Aktivitas</span>
                    </a>

                    <div class="user-dropdown-divider"></div>

                    <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Yakin ingin logout?')">
                        @csrf
                        <button type="submit" class="user-dropdown-item user-dropdown-logout">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- PAGE CONTENT -->
    <main class="page-content">
        @yield('content')
    </main>

</div>

<style>
.user-menu-wrap { position: relative; }
.user-menu { cursor: pointer; }

.user-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 10px 28px rgba(0,0,0,0.14);
    min-width: 250px;
    z-index: 100;
    padding: 8px;

    /* Animasi muncul/hilang yang halus, bukan langsung on/off */
    opacity: 0;
    visibility: hidden;
    transform: translateY(-6px);
    transition: opacity 0.16s ease, transform 0.16s ease, visibility 0.16s;
    pointer-events: none;
}
.user-dropdown.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    pointer-events: auto;
}

.user-dropdown-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 10px 12px;
}
.user-dropdown-name { font-size: 0.875rem; font-weight: 700; color: #1e293b; }
.user-dropdown-email { font-size: 0.74rem; color: #94a3b8; margin-top: 1px; }

.user-dropdown-divider {
    height: 1px;
    background: #f1f5f9;
    margin: 4px 0;
}

.user-dropdown-item {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 9px 10px;
    border-radius: 8px;
    font-size: 0.84rem;
    font-weight: 500;
    color: #475569;
    text-decoration: none;
    background: none;
    border: none;
    font-family: inherit;
    cursor: pointer;
    text-align: left;
}
.user-dropdown-item svg { width: 17px; height: 17px; flex-shrink: 0; color: #94a3b8; }
.user-dropdown-item:hover { background: #f8fafc; color: #1e293b; }
.user-dropdown-item:hover svg { color: #2563eb; }
.user-dropdown-item.active { background: #eff6ff; color: #2563eb; }
.user-dropdown-item.active svg { color: #2563eb; }

.user-dropdown-logout { color: #ef4444; }
.user-dropdown-logout svg { color: #ef4444; }
.user-dropdown-logout:hover { background: #fef2f2; color: #ef4444; }
.user-dropdown-logout:hover svg { color: #ef4444; }

.notif-menu-wrap { position: relative; }
.notif-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 10px 28px rgba(0,0,0,0.14);
    width: 320px;
    max-height: 420px;
    z-index: 100;
    overflow: hidden;
    display: flex;
    flex-direction: column;

    /* Animasi muncul/hilang yang halus, sama seperti user-dropdown */
    opacity: 0;
    visibility: hidden;
    transform: translateY(-6px);
    transition: opacity 0.16s ease, transform 0.16s ease, visibility 0.16s;
    pointer-events: none;
}
.notif-dropdown.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    pointer-events: auto;
}
.notif-dropdown-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 16px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.875rem; font-weight: 700; color: #1e293b;
}
.notif-dropdown-count {
    font-size: 0.7rem; font-weight: 700;
    background: #fee2e2; color: #ef4444;
    padding: 2px 8px; border-radius: 10px;
}
.notif-dropdown-body { overflow-y: auto; max-height: 320px; }
.notif-item {
    display: flex; gap: 10px;
    padding: 12px 16px;
    text-decoration: none;
    border-bottom: 1px solid #f8fafc;
}
.notif-item:hover { background: #f8fafc; }
.notif-item-dot { width: 8px; height: 8px; border-radius: 50%; margin-top: 5px; flex-shrink: 0; }
.notif-item-title { font-size: 0.82rem; font-weight: 600; color: #1e293b; }
.notif-item-sub { font-size: 0.75rem; color: #64748b; margin-top: 2px; }
.notif-item-time { font-size: 0.68rem; color: #94a3b8; margin-top: 3px; }

.notif-empty {
    padding: 36px 16px;
    text-align: center;
    color: #94a3b8;
    font-size: 0.8rem;
}
.notif-empty svg { color: #cbd5e1; }
.notif-empty p { margin-top: 4px; }

.notif-dropdown-footer {
    padding: 10px 16px;
    border-top: 1px solid #f1f5f9;
    text-align: center;
}
.notif-dropdown-footer a {
    font-size: 0.8rem; font-weight: 600; color: #2563eb; text-decoration: none;
}
</style>

<script>
function toggleSidebar() {
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const wrapper  = document.getElementById('mainWrapper');
    sidebar.classList.toggle('collapsed');
    overlay.classList.toggle('active');
    wrapper.classList.toggle('expanded');
}

function toggleSubmenu(el) {
    const group = el.closest('.nav-item-group');
    group.classList.toggle('open');
}

function toggleUserDropdown(event) {
    event.stopPropagation();
    const dropdown = document.getElementById('userDropdown');
    const notifDropdown = document.getElementById('notifDropdown');
    const willOpen = !dropdown.classList.contains('active');

    notifDropdown.classList.remove('active');
    dropdown.classList.toggle('active', willOpen);
}

function toggleNotifDropdown(event) {
    event.stopPropagation();
    const dropdown = document.getElementById('notifDropdown');
    const userDropdown = document.getElementById('userDropdown');
    const willOpen = !dropdown.classList.contains('active');

    userDropdown.classList.remove('active');
    dropdown.classList.toggle('active', willOpen);
}

// Tutup semua dropdown jika klik di luar area keduanya
document.addEventListener('click', function(e) {
    const userWrap  = document.getElementById('userMenuWrap');
    const notifWrap = document.getElementById('notifMenuWrap');

    if (userWrap && !userWrap.contains(e.target)) {
        document.getElementById('userDropdown').classList.remove('active');
    }
    if (notifWrap && !notifWrap.contains(e.target)) {
        document.getElementById('notifDropdown').classList.remove('active');
    }
});

// Tutup dropdown otomatis saat tekan Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('userDropdown')?.classList.remove('active');
        document.getElementById('notifDropdown')?.classList.remove('active');
    }
});
</script>

@stack('scripts')
</body>
</html>