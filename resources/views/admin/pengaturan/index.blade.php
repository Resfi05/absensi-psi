@extends('layouts.admin')
@section('title', 'Pengaturan Akun')
@section('page-title', 'Pengaturan Akun')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-current">Pengaturan Akun</span>
        </nav>
        <h2 class="page-heading">Pengaturan <span class="heading-accent">Akun</span></h2>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" id="flashAlert">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    {{ session('success') }}
    <button class="alert-close" onclick="this.parentElement.remove()">✕</button>
</div>
@endif
@if(session('error'))
<div class="alert alert-error" id="flashAlertError">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    {{ session('error') }}
    <button class="alert-close" onclick="this.parentElement.remove()">✕</button>
</div>
@endif

<div class="pgt-grid">

    {{-- ── KOLOM KIRI: Foto + Ringkasan ──────────── --}}
    <div class="pgt-col-side">
        <div class="pgt-profile-card">
            <div class="pgt-cover"></div>
            <div class="pgt-avatar-wrap">
                <div class="pgt-avatar" id="avatarPreviewWrap">
                    @if($user->foto)
                        <img src="{{ Storage::url($user->foto) }}" alt="{{ $user->name }}" id="avatarPreviewImg">
                    @else
                        <span id="avatarInitial">{{ strtoupper(substr($user->name,0,1)) }}</span>
                    @endif
                </div>
                <form method="POST" action="{{ route('admin.pengaturan.profil') }}" enctype="multipart/form-data" id="fotoForm">
                    @csrf
                    <input type="file" name="foto" id="fotoInput" accept="image/*" style="display:none" onchange="previewAndSubmitFoto(this)">
                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">
                    <input type="hidden" name="no_hp" value="{{ $user->no_hp }}">
                    <button type="button" class="pgt-avatar-edit" onclick="document.getElementById('fotoInput').click()" title="Ganti foto">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/>
                            <circle cx="12" cy="13" r="4"/>
                        </svg>
                    </button>
                </form>
            </div>

            <div class="pgt-profile-name">{{ $user->name }}</div>
            <div class="pgt-profile-role">
                <span class="badge-role {{ $user->role === 'admin' ? 'badge-purple' : 'badge-blue' }}">
                    {{ $user->role === 'admin' ? 'Super Admin' : 'Petugas' }}
                </span>
            </div>

            <div class="pgt-profile-meta">
                <div class="pgt-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <span>{{ $user->email }}</span>
                </div>
                <div class="pgt-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                    <span>{{ $user->no_hp ?? 'Belum diatur' }}</span>
                </div>
                
                {{-- 🔥 PERBAIKAN DI SINI 🔥 --}}
                @if($user->spesialisasi && $user->spesialisasi->isNotEmpty())
                <div class="pgt-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/></svg>
                    <span>Spesialisasi: {{ $user->spesialisasi->pluck('nama')->implode(', ') }}</span>
                </div>
                @endif
                {{-- 🔥 AKHIR PERBAIKAN 🔥 --}}
                
            </div>
        </div>
    </div>

    {{-- ── KOLOM KANAN: Form-form ──────────────────── --}}
    <div class="pgt-col-main">

        {{-- Edit Profil --}}
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
                Informasi Profil
            </div>
            <form method="POST" action="{{ route('admin.pengaturan.profil') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-card-body">
                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label required">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}">
                            @error('name')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}">
                            @error('email')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="no_hp" class="form-control"
                               value="{{ old('no_hp', $user->no_hp) }}" placeholder="08xxxxxxxxxx">
                    </div>
                    <div style="display:flex;justify-content:flex-end;">
                        <button type="submit" class="btn-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v14a2 2 0 01-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Ganti Password --}}
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
                Keamanan & Password
            </div>
            <form method="POST" action="{{ route('admin.pengaturan.password') }}">
                @csrf
                <div class="form-card-body">
                    <div class="form-group">
                        <label class="form-label required">Password Saat Ini</label>
                        <input type="password" name="password_lama" class="form-control @error('password_lama') is-invalid @enderror" placeholder="Masukkan password lama Anda">
                        @error('password_lama')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label required">Password Baru</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min. 6 karakter">
                            @error('password')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                        </div>
                    </div>
                    <div style="display:flex;justify-content:flex-end;">
                        <button type="submit" class="btn-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
                            </svg>
                            Ubah Password
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Preferensi Notifikasi --}}
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/>
                </svg>
                Preferensi Notifikasi
            </div>
            <form method="POST" action="{{ route('admin.pengaturan.notifikasi') }}">
                @csrf
                <div class="form-card-body">
                    <div class="toggle-group">
                        <div class="toggle-info">
                            <div class="toggle-label">Notifikasi Email — Ada Temuan Rusak</div>
                            <div class="toggle-desc">Kirim email kepada Anda setiap kali ada laporan barang rusak yang masuk ke Review Hasil.</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="email_notification_preference" value="1"
                                   {{ $user->email_notification_preference ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div style="background:#f1f5f9;border-radius:8px;padding:12px 14px;display:flex;gap:10px;align-items:flex-start;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;color:#64748b;flex-shrink:0;margin-top:1px">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                        </svg>
                        <div style="font-size:0.78rem;color:#475569;line-height:1.5;">
                            Notifikasi badge di sidebar (jumlah temuan menunggu) bersifat wajib dan tidak dapat dimatikan, karena merupakan indikator kerja utama yang membantu Anda memantau tugas yang tertunda.
                        </div>
                    </div>

                    <div style="display:flex;justify-content:flex-end;">
                        <button type="submit" class="btn-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            Simpan Preferensi
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
.pgt-grid {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 20px;
    align-items: start;
}
@media(max-width: 900px) { .pgt-grid { grid-template-columns: 1fr; } }

.pgt-col-side { display: flex; flex-direction: column; gap: 16px; }
.pgt-col-main { display: flex; flex-direction: column; gap: 18px; }

.pgt-profile-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 16px;
    box-shadow: var(--shadow);
    overflow: hidden;
    text-align: center;
    padding-bottom: 22px;
}
.pgt-cover {
    height: 78px;
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #3b82f6 100%);
}
.pgt-avatar-wrap {
    position: relative;
    width: 96px;
    margin: -48px auto 0;
}
.pgt-avatar {
    width: 96px; height: 96px;
    border-radius: 50%;
    background: linear-gradient(135deg,#1e40af,#3b82f6);
    border: 4px solid white;
    box-shadow: 0 4px 14px rgba(0,0,0,0.12);
    display: flex; align-items: center; justify-content: center;
    overflow: hidden;
    color: white;
    font-size: 2.1rem;
    font-weight: 700;
}
.pgt-avatar img { width: 100%; height: 100%; object-fit: cover; }
.pgt-avatar-edit {
    position: absolute;
    bottom: -2px; right: -2px;
    width: 32px; height: 32px;
    border-radius: 50%;
    background: #2563eb;
    border: 3px solid white;
    color: white;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(37,99,235,0.4);
}
.pgt-avatar-edit svg { width: 14px; height: 14px; }
.pgt-avatar-edit:hover { background: #1d4ed8; }

.pgt-profile-name { font-size: 1.05rem; font-weight: 800; color: var(--text-main); margin-top: 14px; }
.pgt-profile-role { margin-top: 6px; }

.pgt-profile-meta {
    margin-top: 18px;
    padding: 0 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    text-align: left;
}
.pgt-meta-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.78rem;
    color: var(--text-muted);
}
.pgt-meta-item svg { width: 15px; height: 15px; flex-shrink: 0; color: #94a3b8; }
</style>
@endpush

@push('scripts')
<script>
function previewAndSubmitFoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const wrap = document.getElementById('avatarPreviewWrap');
            wrap.innerHTML = `<img src="${e.target.result}" id="avatarPreviewImg">`;
        };
        reader.readAsDataURL(input.files[0]);
        document.getElementById('fotoForm').submit();
    }
}

setTimeout(() => {
    const a = document.getElementById('flashAlert');
    if (a) a.style.opacity = '0', setTimeout(() => a.remove(), 400);
    const e = document.getElementById('flashAlertError');
    if (e) e.style.opacity = '0', setTimeout(() => e.remove(), 400);
}, 5000);
</script>
@endpush