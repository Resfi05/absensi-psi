<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        @if(isset($barang) && $barang)
            Portal Inventaris - {{ $barang->nama_barang }}
        @else
            Scan QR Code - PT Padma Soode Indonesia
        @endif
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --dark: #0f172a;
            --danger: #ef4444;
            --p-muted: #64748b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

        body.mode-portal {
            background: url("{{ asset('images/gedung.png') }}") center center/cover no-repeat fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.55);
            z-index: 1;
        }

        .portal-card {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            width: 100%;
            max-width: 420px;
            border-radius: 32px;
            padding: 45px 28px;
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.4);
            text-align: center;
            animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes fadeUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .pt-logo { height: 52px; margin-bottom: 22px; }

        .item-info {
            background: rgba(255, 255, 255, 0.6);
            border-radius: 20px;
            padding: 20px 16px;
            margin-bottom: 28px;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .item-code {
            display: inline-block;
            background: #dbeafe;
            color: var(--primary);
            font-size: 0.78rem;
            font-weight: 800;
            padding: 5px 14px;
            border-radius: 20px;
            margin-bottom: 10px;
        }

        .item-name { font-size: 1.4rem; font-weight: 800; color: var(--dark); margin-bottom: 8px; line-height: 1.2; }
        .item-desc { font-size: 0.82rem; color: #475569; font-weight: 600; display: flex; flex-direction: column; gap: 4px; align-items: center; }
        .action-title { font-size: 0.95rem; font-weight: 700; color: var(--dark); margin-bottom: 14px; }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            padding: 17px 20px;
            border-radius: 18px;
            font-size: 0.92rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.25s ease;
            margin-bottom: 12px;
            border: none;
            cursor: pointer;
        }

        .btn-petugas {
            background: var(--primary);
            color: white;
            box-shadow: 0 8px 20px -6px rgba(37, 99, 235, 0.4);
        }
        .btn-petugas:hover { background: var(--primary-hover); transform: translateY(-2px); }

        .btn-auditor {
            background: rgba(255, 255, 255, 0.7);
            color: var(--dark);
            border: 2px solid rgba(255, 255, 255, 0.9);
        }
        .btn-auditor:hover { background: white; transform: translateY(-2px); }
        .btn svg { width: 20px; height: 20px; flex-shrink: 0; }

        .footer-text { margin-top: 20px; font-size: 0.72rem; color: #64748b; font-weight: 600; }

        /* ── MODAL LOGIN ── */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
            z-index: 100;
            align-items: center;
            justify-content: center;
            padding: 20px;
            backdrop-filter: blur(4px);
        }
        .modal-backdrop.active { display: flex; animation: fadeIn 0.2s ease; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .modal-box {
            background: white;
            border-radius: 24px;
            padding: 32px 28px;
            width: 100%;
            max-width: 380px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to   { transform: translateY(0); opacity: 1; }
        }

        .modal-header { text-align: center; margin-bottom: 24px; }

        .modal-icon {
            width: 56px; height: 56px;
            background: #eff6ff;
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
            color: var(--primary);
        }
        .modal-icon svg { width: 28px; height: 28px; }
        .modal-title { font-size: 1.1rem; font-weight: 800; color: var(--dark); margin-bottom: 4px; }
        .modal-sub   { font-size: 0.8rem; color: var(--p-muted); }

        .form-group { margin-bottom: 16px; text-align: left; }
        .form-label { display: block; font-size: 0.8rem; font-weight: 700; color: var(--dark); margin-bottom: 6px; }
        .form-input {
            width: 100%;
            padding: 13px 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.9rem;
            font-family: inherit;
            color: var(--dark);
            transition: border-color 0.2s;
            background: #f8fafc;
        }
        .form-input:focus { outline: none; border-color: var(--primary); background: white; }

        .error-box {
            display: none;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.8rem;
            color: var(--danger);
            font-weight: 600;
            margin-bottom: 14px;
            text-align: center;
        }
        .error-box.show { display: block; }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 0.95rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-login:hover { background: var(--primary-hover); }
        .btn-login:disabled { opacity: 0.7; cursor: not-allowed; }

        .btn-cancel {
            width: 100%;
            padding: 12px;
            background: none;
            border: none;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--p-muted);
            cursor: pointer;
            margin-top: 8px;
            font-family: inherit;
        }
        .btn-cancel:hover { color: var(--dark); }

        /* ── MODE KAMERA ── */
        body.mode-kamera { background: #f8fafc; min-height: 100vh; }
        .kamera-wrap { max-width: 480px; margin: 0 auto; padding: 20px 16px; }
        .kamera-header h1 { font-size: 1.25rem; font-weight: 800; color: var(--dark); margin-bottom: 4px; }
        .kamera-header p  { font-size: 0.82rem; color: var(--p-muted); margin-bottom: 18px; }
        .accent { color: var(--primary); }
        .scanner-box { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.08); margin-bottom: 14px; }
        #qr-reader { width: 100%; }
        #scanStatus { text-align: center; padding: 14px; color: var(--p-muted); font-size: 0.85rem; font-weight: 500; }
        .tip-banner { display: flex; align-items: flex-start; gap: 10px; background: #eff6ff; border-radius: 12px; padding: 14px 16px; }
        .tip-icon { width: 32px; height: 32px; border-radius: 8px; background: rgba(255,255,255,0.7); display: flex; align-items: center; justify-content: center; font-size: 1rem; }
        .tip-title { font-size: 0.82rem; font-weight: 700; color: #1e40af; }
        .tip-sub   { font-size: 0.75rem; color: #3b82f6; margin-top: 2px; }

        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner { width: 18px; height: 18px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.7s linear infinite; }
    </style>
</head>

@if(isset($barang) && $barang)
{{-- ════════ MODE PORTAL ════════ --}}
<body class="mode-portal">
    <div class="overlay"></div>

    <div class="portal-card">
        <img src="{{ asset('images/logo.png') }}" alt="PT Padma Soode Indonesia" class="pt-logo">

        <div class="item-info">
            <span class="item-code">{{ $barang->kode_barang }}</span>
            <h1 class="item-name">{{ $barang->nama_barang }}</h1>
            <div class="item-desc">
                <span>📍 {{ $barang->lokasiRelasi->nama ?? 'Lokasi Tidak Diketahui' }}</span>
                <span>🏷️ {{ $barang->kategori->nama ?? 'Tanpa Kategori' }}</span>
            </div>
        </div>

        <h3 class="action-title">Pilih Akses Anda:</h3>

        {{--
            LOGIKA TOMBOL PETUGAS:
            - Sudah login sebagai 'user' → langsung submit form prosesPetugas (tanpa modal)
            - Belum login / login sebagai admin → tampilkan modal login
        --}}
        @auth
            @if(Auth::user()->role === 'user')
                {{-- Petugas sudah login → langsung proses --}}
                <form method="POST"
                      action="{{ route('user.scan.proses') }}{{ isset($jadwalTarget) && $jadwalTarget ? '?jadwal='.$jadwalTarget : '' }}">
                    @csrf
                    <button type="submit" class="btn btn-petugas">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        <span>Petugas (Checklist)</span>
                    </button>
                </form>
            @else
                {{-- Login sebagai admin / role lain → tampilkan modal (butuh akun petugas) --}}
                <button type="button" class="btn btn-petugas" onclick="bukaModalLogin()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    <span>Petugas (Checklist)</span>
                </button>
            @endif
        @else
            {{-- Belum login sama sekali → tampilkan modal login --}}
            <button type="button" class="btn btn-petugas" onclick="bukaModalLogin()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                <span>Petugas (Checklist)</span>
            </button>
        @endauth

        {{-- Tombol Auditor → publik, tidak perlu login --}}
        <a href="{{ route('auditor.story', $barang->kode_barang) }}" class="btn btn-auditor">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="18" y1="20" x2="18" y2="10"/>
                <line x1="12" y1="20" x2="12" y2="4"/>
                <line x1="6"  y1="20" x2="6"  y2="14"/>
            </svg>
            <span>Auditor / Riwayat Pengecekan</span>
        </a>

        <div class="footer-text">
            Sistem Informasi Inventaris &copy; {{ date('Y') }} PT Padma Soode Indonesia
        </div>
    </div>

    {{-- ════════ MODAL LOGIN ════════ --}}
    {{-- Hanya muncul kalau belum login / login sebagai non-petugas --}}
    <div class="modal-backdrop" id="modalLogin">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                </div>
                <div class="modal-title">Login Petugas</div>
                <div class="modal-sub">Masukkan kredensial akun petugas Anda</div>
            </div>

            <div class="error-box" id="errorBox">
                ⚠️ <span id="errorMsg">Email atau password salah.</span>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" id="loginEmail" class="form-input"
                       placeholder="email@padmasoode.com"
                       autocomplete="email">
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" id="loginPassword" class="form-input"
                       placeholder="••••••••"
                       autocomplete="current-password"
                       onkeydown="if(event.key==='Enter') prosesLogin()">
            </div>

            <button type="button" class="btn-login" id="btnLogin" onclick="prosesLogin()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;">
                    <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/>
                    <polyline points="10 17 15 12 10 7"/>
                    <line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
                Masuk & Lanjutkan
            </button>

            <button type="button" class="btn-cancel" onclick="tutupModalLogin()">Batal</button>
        </div>
    </div>

    {{-- Form tersembunyi untuk submit setelah login berhasil --}}
    <form id="formPetugas" method="POST"
          action="{{ route('user.scan.proses') }}{{ isset($jadwalTarget) && $jadwalTarget ? '?jadwal='.$jadwalTarget : '' }}"
          style="display:none;">
        @csrf
    </form>

    <script>
        function bukaModalLogin() {
            document.getElementById('modalLogin').classList.add('active');
            setTimeout(() => document.getElementById('loginEmail').focus(), 300);
        }

        function tutupModalLogin() {
            document.getElementById('modalLogin').classList.remove('active');
            document.getElementById('errorBox').classList.remove('show');
            document.getElementById('loginEmail').value   = '';
            document.getElementById('loginPassword').value = '';
        }

        document.getElementById('modalLogin').addEventListener('click', function(e) {
            if (e.target === this) tutupModalLogin();
        });

        async function prosesLogin() {
            const email    = document.getElementById('loginEmail').value.trim();
            const password = document.getElementById('loginPassword').value;
            const btn      = document.getElementById('btnLogin');
            const errorBox = document.getElementById('errorBox');
            const errorMsg = document.getElementById('errorMsg');

            if (!email || !password) {
                errorMsg.textContent = 'Email dan password wajib diisi.';
                errorBox.classList.add('show');
                return;
            }

            btn.disabled    = true;
            btn.innerHTML   = '<div class="spinner"></div> Memverifikasi...';
            errorBox.classList.remove('show');

            try {
                const res = await fetch("{{ route('scan.login.ajax') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type':  'application/json',
                        'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ email, password }),
                });

                const data = await res.json();

                if (data.success) {
                    btn.innerHTML = '✅ Login berhasil, mengalihkan...';
                    setTimeout(() => document.getElementById('formPetugas').submit(), 600);
                } else {
                    errorMsg.textContent = data.message ?? 'Email atau password salah.';
                    errorBox.classList.add('show');
                    btn.disabled  = false;
                    btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg> Masuk & Lanjutkan';
                }
            } catch (err) {
                errorMsg.textContent = 'Kesalahan jaringan. Coba lagi.';
                errorBox.classList.add('show');
                btn.disabled  = false;
                btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg> Masuk & Lanjutkan';
            }
        }
    </script>
</body>

@else
{{-- ════════ MODE KAMERA ════════ --}}
<body class="mode-kamera">
    <div class="kamera-wrap">
        <div class="kamera-header">
            <h1>Scan <span class="accent">QR Code</span></h1>
            <p>
                @if(isset($jadwalTarget) && $jadwalTarget)
                    Pindai QR Code pada barang untuk memulai tugas ini
                @else
                    Arahkan kamera ke stiker QR Code pada barang
                @endif
            </p>
        </div>

        <div class="scanner-box">
            <div id="qr-reader"></div>
        </div>

        <div id="scanStatus">Menyiapkan kamera...</div>

        <div class="tip-banner">
            <div class="tip-icon">💡</div>
            <div>
                <div class="tip-title">Tips Scan</div>
                <div class="tip-sub">Pastikan QR Code terlihat jelas dan tidak buram untuk hasil terbaik.</div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <script>
        const statusEl     = document.getElementById('scanStatus');
        const jadwalTarget = @json($jadwalTarget ?? null);
        let isProcessing   = false;

        function onScanSuccess(decodedText) {
            if (isProcessing) return;
            isProcessing = true;

            statusEl.innerHTML   = '✅ QR Code terdeteksi, memproses...';
            statusEl.style.color = 'green';

            html5QrCode.stop().then(() => {
                let token;
                try {
                    const url   = new URL(decodedText);
                    const parts = url.pathname.split('/');
                    token = parts[parts.length - 1];
                } catch (e) {
                    token = decodedText;
                }

                let target = "{{ url('/user/scan/validasi') }}/" + token;
                if (jadwalTarget) target += "?jadwal=" + jadwalTarget;
                window.location.href = target;
            }).catch(() => {
                isProcessing         = false;
                statusEl.innerHTML   = 'Arahkan kamera ke QR Code...';
                statusEl.style.color = '';
            });
        }

        const html5QrCode = new Html5Qrcode("qr-reader");

        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            onScanSuccess,
            () => {}
        ).then(() => {
            statusEl.innerHTML = 'Arahkan kamera ke QR Code...';
        }).catch(() => {
            statusEl.innerHTML   = '⚠️ Tidak bisa mengakses kamera. Pastikan izin kamera diaktifkan.';
            statusEl.style.color = 'red';
        });
    </script>
</body>
@endif

</html>