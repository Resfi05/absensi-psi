<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR Code — {{ $qrcode->barang->kode_barang }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .print-wrap {
            display: flex;
            flex-direction: column;
            gap: 20px;
            align-items: center;
        }

        /* QR Card - ukuran label */
        .qr-label {
            background: white;
            border-radius: 16px;
            padding: 24px;
            width: 320px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
            text-align: center;
            border: 2px solid #e2e8f0;
        }

        .qr-label-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f1f5f9;
        }

        .company-logo-text {
            font-size: 0.7rem;
            font-weight: 800;
            color: #1e40af;
            letter-spacing: 0.5px;
            line-height: 1.3;
            text-align: left;
        }

        .qr-image-wrap {
            background: #f8fafc;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
            display: inline-block;
        }

        .qr-image-wrap img {
            width: 180px;
            height: 180px;
            display: block;
        }

        .qr-label-kode {
            font-size: 1.1rem;
            font-weight: 800;
            color: #1e40af;
            font-family: monospace;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .qr-label-nama {
            font-size: 0.875rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .qr-label-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
            background: #f8fafc;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 12px;
        }

        .qr-label-info-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.75rem;
            color: #64748b;
        }

        .qr-label-info-row strong {
            color: #1e293b;
            min-width: 60px;
        }

        .qr-label-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 700;
            margin-top: 4px;
        }
        .badge-ac   { background: #dbeafe; color: #2563eb; }
        .badge-apar { background: #fee2e2; color: #ef4444; }

        .qr-scan-hint {
            font-size: 0.7rem;
            color: #94a3b8;
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px solid #f1f5f9;
        }

        /* Print actions */
        .print-actions {
            display: flex;
            gap: 12px;
        }

        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #1e40af;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-print:hover { background: #1e3a8a; }
        .btn-print svg { width: 16px; height: 16px; }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: white;
            color: #1e293b;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-back svg { width: 16px; height: 16px; }

        /* Print CSS */
        @media print {
            body { background: white; padding: 0; }
            .print-actions { display: none; }
            .qr-label {
                box-shadow: none;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
            }
        }
    </style>
</head>
<body>
<div class="print-wrap">

    {{-- QR Label Card --}}
    <div class="qr-label">
        <div class="qr-label-header">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height:36px;object-fit:contain;">
            <div class="company-logo-text">
                PT PADMA SOODE<br>INDONESIA
            </div>
        </div>

        <div class="qr-image-wrap">
            <img src="{{ asset('storage/' . $qrcode->qr_code_path) }}"
                 alt="QR Code {{ $qrcode->barang->kode_barang }}">
        </div>

        <div class="qr-label-kode">{{ $qrcode->barang->kode_barang }}</div>
        <div class="qr-label-nama">{{ $qrcode->barang->nama_barang }}</div>

        <span class="qr-label-badge {{ $qrcode->barang->jenis_barang === 'AC' ? 'badge-ac' : 'badge-apar' }}">
            {{ $qrcode->barang->jenis_barang }}
        </span>

        <div class="qr-label-info" style="margin-top:12px">
            @if($qrcode->barang->lokasi)
            <div class="qr-label-info-row">
                <strong>Lokasi</strong>
                <span>{{ $qrcode->barang->lokasi }}</span>
            </div>
            @endif
            @if($qrcode->barang->gedung)
            <div class="qr-label-info-row">
                <strong>Gedung</strong>
                <span>{{ $qrcode->barang->gedung }}</span>
            </div>
            @endif
            @if($qrcode->barang->ruangan)
            <div class="qr-label-info-row">
                <strong>Ruangan</strong>
                <span>{{ $qrcode->barang->ruangan }}</span>
            </div>
            @endif
        </div>

        <div class="qr-scan-hint">
            📱 Scan QR Code ini untuk memulai pengecekan
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="print-actions">
        <button onclick="window.print()" class="btn-print">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 6 2 18 2 18 9"/>
                <path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/>
                <rect x="6" y="14" width="12" height="8"/>
            </svg>
            Cetak Sekarang
        </button>
        <a href="{{ route('admin.qrcode.download', $qrcode->id) }}" class="btn-back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Download SVG
        </a>
        <a href="{{ route('admin.qrcode.index') }}" class="btn-back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
            </svg>
            Kembali
        </a>
    </div>

</div>
</body>
</html>