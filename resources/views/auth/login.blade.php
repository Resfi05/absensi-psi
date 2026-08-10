<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — PT Padma Soode Indonesia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

<div class="login-wrapper">

    {{-- ══════════ PANEL KIRI ══════════ --}}
    <div class="left-panel">
        <div class="left-overlay"></div>
        <img src="{{ asset('images/gedung.png') }}"
             alt="Gedung PT Padma Soode Indonesia"
             class="bg-image">

        <div class="left-content">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Soode" class="logo">

            <div class="brand-card">
                <h1 class="brand-title">
                    Sistem Absensi<br>
                    <span class="brand-accent">Pengecekan Barang</span>
                </h1>
                <p class="brand-desc">
                    Platform digital terintegrasi untuk manajemen monitoring, pengecekan rutin, 
                    dan pemeliharaan seluruh aset serta inventaris fisik perusahaan secara terjadwal.
                </p>
                <div class="badge-row">
                    <div class="badge">
                        <span class="badge-icon">📦</span>
                        <div>
                            <div class="badge-label">Multi Aset</div>
                            <div class="badge-sub">Semua Inventaris Perusahaan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════ PANEL KANAN ══════════ --}}
    <div class="right-panel">
        <div class="dots-bg"></div>

        <div class="form-container">

            <div class="form-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                    <rect x="9" y="3" width="6" height="4" rx="1"/>
                    <path d="M9 12l2 2 4-4"/>
                </svg>
            </div>

            <h2 class="form-title">Selamat Datang!</h2>
            <p class="form-subtitle">Silakan masuk untuk melanjutkan</p>

            {{-- Alert Error --}}
            @if ($errors->any())
                <div class="alert-error">
                    <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Alert Success (setelah logout) --}}
            @if (session('success'))
                <div class="alert-success">
                    <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="login-form">
                @csrf

                {{-- Username --}}
                <div class="field-group">
                    <label for="username">Username</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </span>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="{{ old('username') }}"
                            placeholder="Masukkan username Anda"
                            autocomplete="username"
                            autofocus
                            class="{{ $errors->has('username') ? 'is-error' : '' }}"
                        >
                    </div>
                </div>

                {{-- Password --}}
                <div class="field-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0110 0v4"/>
                            </svg>
                        </span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan password Anda"
                            autocomplete="current-password"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <svg id="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg id="eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none">
                                <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember --}}
                <div class="form-options">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="checkmark"></span>
                        Ingat saya
                    </label>
                </div>

                <button type="submit" class="btn-masuk">
                    <span>Masuk</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>
            </form>

            <div class="system-footer-info">
                <div class="system-info">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                        <rect x="9" y="3" width="6" height="4" rx="1"/>
                        <path d="M9 12l2 2 4-4"/>
                    </svg>
                    Sistem Absensi Pengecekan Barang
                </div>
            </div>
        </div>

        <p class="copyright">© 2026 PT Padma Soode Indonesia. All rights reserved. <span class="developer-initials">[R.A.A]</span></p>
    </div>

</div>

<script>
function togglePassword() {
    const input   = document.getElementById('password');
    const eyeOpen = document.getElementById('eye-open');
    const eyeClosed = document.getElementById('eye-closed');
    if (input.type === 'password') {
        input.type = 'text';
        eyeOpen.style.display   = 'none';
        eyeClosed.style.display = 'block';
    } else {
        input.type = 'password';
        eyeOpen.style.display   = 'block';
        eyeClosed.style.display = 'none';
    }
}
</script>

</body>
</html>