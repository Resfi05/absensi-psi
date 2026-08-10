<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Petugas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/user-app.css') }}">
    @stack('styles')
</head>
<body>

<div class="app-shell">

    {{-- ── TOP BAR ──────────────────────────────────── --}}
    <header class="app-topbar">
        <div class="app-topbar-left">
            {{-- Hamburger dihapus sesuai keputusan (semua menu sudah di bottom nav) --}}
            <div class="app-brand">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="app-brand-logo">
                <div>
                    <div class="app-brand-name">PT PADMA SOODE</div>
                    <div class="app-brand-sub">INDONESIA</div>
                </div>
            </div>
        </div>
        <div class="app-topbar-right">
            {{-- Bell icon — buka panel notifikasi --}}
            <button class="app-icon-btn" onclick="toggleNotifPanel()" style="position:relative;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 01-3.46 0"/>
                </svg>
                @if(($notifBadge ?? 0) > 0)
                    <span class="app-icon-badge">{{ $notifBadge > 9 ? '9+' : $notifBadge }}</span>
                @endif
            </button>
            <a href="{{ route('user.akun.index') }}" class="app-avatar-link">
                <div class="app-avatar">
                    @if(auth()->user()->foto)
                        <img src="{{ Storage::url(auth()->user()->foto) }}" alt="">
                    @else
                        {{ strtoupper(substr(auth()->user()->name,0,1)) }}
                    @endif
                </div>
            </a>
        </div>
    </header>

    {{-- ── NOTIFIKASI PANEL (slide-in dari atas) ───── --}}
    <div id="notifOverlay" onclick="tutupNotifPanel()"
         style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.3);z-index:200;"></div>

    <div id="notifPanel"
         style="position:fixed;top:0;left:0;right:0;z-index:201;
                background:var(--p-surface);border-radius:0 0 20px 20px;
                box-shadow:0 8px 32px rgba(0,0,0,0.18);
                transform:translateY(-100%);transition:transform 0.28s cubic-bezier(.4,0,.2,1);
                max-height:80vh;display:flex;flex-direction:column;">

        {{-- Header panel --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 18px 12px;border-bottom:1px solid var(--p-border);">
            <div style="font-size:1rem;font-weight:800;color:var(--p-text);">Notifikasi</div>
            <div style="display:flex;align-items:center;gap:10px;">
                @if(($notifBadge ?? 0) > 0)
                    <button onclick="tandaiSemuaDibaca()" style="font-size:0.72rem;color:var(--p-primary);font-weight:600;background:none;border:none;cursor:pointer;font-family:inherit;">
                        Tandai semua dibaca
                    </button>
                @endif
                <button onclick="tutupNotifPanel()" style="width:28px;height:28px;border-radius:50%;background:var(--p-bg);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--p-muted);">✕</button>
            </div>
        </div>

        {{-- Daftar notifikasi --}}
        <div style="overflow-y:auto;flex:1;">
            @forelse(($notifList ?? []) as $notif)
            <a href="{{ $notif->link ? route($notif->link) : route('user.jadwal.index') }}"
               onclick="tandaiDibaca({{ $notif->id }})"
               style="display:flex;align-items:flex-start;gap:12px;padding:14px 18px;border-bottom:1px solid var(--p-border);text-decoration:none;
                      background:{{ $notif->is_read ? 'transparent' : 'var(--p-primary-lt)' }};">
                <div style="width:38px;height:38px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;
                            background:{{ $notif->tipe === 'jadwal_baru' ? 'var(--p-primary-lt)' : 'var(--p-warning-lt)' }};
                            color:{{ $notif->tipe === 'jadwal_baru' ? 'var(--p-primary)' : 'var(--p-warning)' }};">
                    @if($notif->tipe === 'jadwal_baru')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;">
                            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    @else
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    @endif
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:0.84rem;font-weight:{{ $notif->is_read ? '500' : '700' }};color:var(--p-text);margin-bottom:2px;">
                        {{ $notif->judul }}
                        @if(!$notif->is_read)
                            <span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:var(--p-primary);margin-left:5px;vertical-align:middle;"></span>
                        @endif
                    </div>
                    <div style="font-size:0.75rem;color:var(--p-muted);line-height:1.4;">{{ $notif->pesan }}</div>
                    <div style="font-size:0.68rem;color:var(--p-faint);margin-top:4px;">
                        {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                    </div>
                </div>
            </a>
            @empty
            <div style="padding:48px 24px;text-align:center;color:var(--p-muted);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                     style="width:48px;height:48px;margin:0 auto 12px;opacity:0.4;display:block;">
                    <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 01-3.46 0"/>
                </svg>
                <div style="font-size:0.85rem;font-weight:600;">Belum ada notifikasi</div>
                <div style="font-size:0.75rem;margin-top:4px;">Notifikasi jadwal baru akan muncul di sini</div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ── PAGE CONTENT ─────────────────────────────── --}}
    <main class="app-content">
        @yield('content')
    </main>

    {{-- ── BOTTOM NAVIGATION ────────────────────────── --}}
    <nav class="app-bottom-nav">
        <a href="{{ route('user.dashboard') }}" class="bn-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('user.jadwal.index') }}" class="bn-item {{ request()->routeIs('user.jadwal*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span>Jadwal</span>
        </a>
        <a href="{{ route('user.scan') }}" class="bn-item bn-item-scan">
            <span class="bn-scan-circle">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 7V5a2 2 0 012-2h2M17 3h2a2 2 0 012 2v2M21 17v2a2 2 0 01-2 2h-2M7 21H5a2 2 0 01-2-2v-2"/>
                    <rect x="7" y="7" width="10" height="10" rx="1"/>
                </svg>
            </span>
        </a>
        <a href="{{ route('user.riwayat') }}" class="bn-item {{ request()->routeIs('user.riwayat*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
            </svg>
            <span>Riwayat</span>
        </a>
        <a href="{{ route('user.akun.index') }}" class="bn-item {{ request()->routeIs('user.akun*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
            <span>Akun</span>
        </a>
    </nav>

</div>

<script>
// ── Panel Notifikasi ──
function toggleNotifPanel() {
    const panel   = document.getElementById('notifPanel');
    const overlay = document.getElementById('notifOverlay');
    const isOpen  = panel.style.transform === 'translateY(0%)';

    if (isOpen) {
        tutupNotifPanel();
    } else {
        overlay.style.display = 'block';
        panel.style.transform = 'translateY(0%)';
    }
}

function tutupNotifPanel() {
    document.getElementById('notifPanel').style.transform = 'translateY(-100%)';
    setTimeout(() => {
        document.getElementById('notifOverlay').style.display = 'none';
    }, 280);
}

// Tandai 1 notifikasi dibaca via AJAX
function tandaiDibaca(id) {
    fetch(`/user/notifikasi/${id}/baca`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        }
    });
    // Hapus dot biru dari item ini (tanpa reload)
    event.currentTarget?.querySelector('span[style*="border-radius:50%"]')?.remove();
}

// Tandai semua dibaca
function tandaiSemuaDibaca() {
    fetch('/user/notifikasi/baca-semua', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        }
    }).then(() => {
        // Hilangkan badge dan semua dot biru, tutup panel
        document.querySelectorAll('.app-icon-badge').forEach(el => el.remove());
        tutupNotifPanel();
    });
}
</script>

@stack('scripts')
</body>
</html>