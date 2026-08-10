@extends('layouts.admin')
@section('title', 'Detail Barang — ' . $barang->kode_barang)
@section('page-title', 'Detail Barang')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-sep">/</span>
            <a href="{{ route('admin.barang.index') }}" class="breadcrumb-link">Kelola Barang</a>
            <span class="breadcrumb-sep">/</span>
            <span class="breadcrumb-current">{{ $barang->kode_barang }}</span>
        </nav>
        <h2 class="page-heading">Detail <span class="heading-accent">{{ $barang->kode_barang }}</span></h2>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('admin.barang.edit', $barang->id) }}" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Edit Barang
        </a>
        <a href="{{ route('admin.barang.index') }}" class="btn-secondary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
            </svg>
            Kembali
        </a>
    </div>
</div>

<div class="show-grid">
    <div class="show-col-main">

        {{-- Info Barang --}}
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="3" width="20" height="14" rx="2"/>
                    <line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
                </svg>
                Informasi Barang
            </div>
            <div class="form-card-body" style="gap:0;padding:0;">
                <table class="detail-table">
                    <tr>
                        <td class="detail-label">Kode Barang</td>
                        <td>
                            <span class="barang-table kode-barang" style="font-family:monospace;font-size:0.85rem;font-weight:700;color:#2563eb;background:#eff6ff;padding:4px 10px;border-radius:6px;">
                                {{ $barang->kode_barang }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="detail-label">Nama Barang</td>
                        <td class="detail-val">{{ $barang->nama_barang }}</td>
                    </tr>
                    <tr>
                        <td class="detail-label">Kategori</td>
                        <td>
                            @if($barang->kategori)
                                <span class="badge-jenis"
                                    style="background:{{ $barang->kategori->warna }}20;color:{{ $barang->kategori->warna }};border:1px solid {{ $barang->kategori->warna }}40;">
                                    {{ $barang->kategori->nama }}
                                </span>
                            @else
                                <span class="badge-jenis badge-gray">{{ $barang->jenis_barang }}</span>
                            @endif
                        </td>
                    </tr>
                    
                    {{-- SPESIFIKASI TAMBAHAN --}}
                    <tr>
                        <td class="detail-label">Merk</td>
                        <td class="detail-val">{{ $barang->merk ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="detail-label">Model</td>
                        <td class="detail-val">{{ $barang->model ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="detail-label">Tipe</td>
                        <td class="detail-val">{{ $barang->tipe ?? '—' }}</td>
                    </tr>
                    
                    <tr>
                        <td class="detail-label">Masa Kedaluwarsa</td>
                        <td>
                            @if($barang->tanggal_expired)
                                @php
                                    $tglExp = \Carbon\Carbon::parse($barang->tanggal_expired);
                                    $now = \Carbon\Carbon::now();
                                    $isExpired = $now->gt($tglExp);
                                    $isNear = !$isExpired && $now->diffInDays($tglExp) <= 30;
                                @endphp

                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-weight: 600; font-size: 0.85rem;">
                                        {{ $tglExp->translatedFormat('d F Y') }}
                                    </span>
                                    
                                    @if($isExpired)
                                        <span class="badge-status badge-red" style="font-size: 0.7rem; padding: 3px 8px;">
                                            ⚠️ SUDAH EXPIRED
                                        </span>
                                    @elseif($isNear)
                                        <span class="badge-status badge-orange" style="font-size: 0.7rem; padding: 3px 8px;">
                                            ⚠️ SEGERA EXPIRED ({{ $now->diffInDays($tglExp) }} hari lagi)
                                        </span>
                                    @else
                                        <span class="badge-status badge-green" style="font-size: 0.7rem; padding: 3px 8px;">
                                            ✓ AMAN
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span style="color: var(--text-muted); font-size: 0.85rem; font-style: italic;">
                                    Tidak Ada (Permanen)
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="detail-label">Status</td>
                        <td>
                            <span class="badge-status {{ $barang->is_active ? 'badge-green' : 'badge-red' }}">
                                {{ $barang->is_active ? 'Aktif' : 'Tidak Aktif / Rusak' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="detail-label">Keterangan Tambahan</td>
                        <td class="detail-val">{{ $barang->keterangan ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="detail-label">Dibuat</td>
                        <td class="detail-val">{{ $barang->created_at->format('d F Y, H:i') }} WIB</td>
                    </tr>
                    <tr>
                        <td class="detail-label">Diperbarui</td>
                        <td class="detail-val">{{ $barang->updated_at->format('d F Y, H:i') }} WIB</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Jejak Riwayat Lokasi --}}
        <div class="form-card">
            <div class="form-card-header" style="background:#f0fdf4; color:#166534; border-bottom: 1px solid #bbf7d0;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                Jejak Riwayat Lokasi & Status
            </div>
            <div class="form-card-body" style="padding: 20px;">
                @if(isset($riwayatBarang) && $riwayatBarang->count() > 0)
                    <div class="timeline-container">
                        @foreach($riwayatBarang as $rb)
                            <div class="timeline-item">
                                <div class="timeline-date">{{ $rb->created_at->translatedFormat('d M Y - H:i') }} WIB</div>
                                <div class="timeline-content">
                                    
                                    @if($rb->lokasi_lama_id != $rb->lokasi_baru_id)
                                        <div style="margin-bottom: 8px;">
                                            <span style="font-weight:700; color:#475569; font-size: 0.8rem; display:block; margin-bottom:4px;">📍 Pindah Lokasi:</span>
                                            <div style="display:flex; align-items:center; gap:8px;">
                                                <span style="background:#f1f5f9; padding:4px 8px; border-radius:4px; font-size:0.75rem; color:#64748b;">{{ $rb->nama_lokasi_lama }}</span>
                                                <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="width:14px;height:14px;"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                                <span style="background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; padding:4px 8px; border-radius:4px; font-size:0.75rem; font-weight:600;">{{ $rb->nama_lokasi_baru }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if($rb->status_lama != $rb->status_baru)
                                        <div>
                                            <span style="font-weight:700; color:#475569; font-size: 0.8rem; display:block; margin-bottom:4px;">🔄 Perubahan Kondisi:</span>
                                            <div style="display:flex; align-items:center; gap:8px;">
                                                <span class="badge-status {{ $rb->status_baru ? 'badge-green' : 'badge-red' }}">
                                                    Menjadi {{ $rb->status_baru ? 'Aktif' : 'Rusak / Non-aktif' }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align:center; padding:20px; color:#94a3b8; font-size:0.85rem;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:40px;height:40px; margin:0 auto 10px; display:block;">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                        </svg>
                        Belum ada riwayat perpindahan atau kerusakan sejak barang dibuat.
                    </div>
                @endif
            </div>
        </div>

        {{-- Riwayat Pengecekan --}}
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                </svg>
                Riwayat Pengecekan Terakhir
            </div>
            <div class="table-wrap">
                @if($barang->pengecekan->count() > 0)
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Petugas</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($barang->pengecekan as $i => $p)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $p->user->name ?? '—' }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal_pengecekan)->format('d M Y, H:i') }}</td>
                            <td>
                                <span class="badge-status {{ $p->status === 'selesai' || $p->status === 'aman' ? 'badge-green' : 'badge-orange' }}">
                                    {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-table-state" style="padding:40px 20px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                    </svg>
                    <p>Belum ada riwayat pengecekan</p>
                </div>
                @endif
            </div>
        </div>

    </div>

    <div class="show-col-side">

        {{-- Lokasi Sekarang (Highlight) --}}
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                Lokasi Saat Ini
            </div>
            <div class="form-card-body" style="align-items:center; text-align:center;">
                @if($barang->lokasiRelasi)
                    <div style="font-size:1.1rem; font-weight:800; color:#2563eb;">📍 {{ $barang->lokasiRelasi->nama }}</div>
                @else
                    <span style="color:#d97706;">{{ $barang->lokasi ?? '—' }} <br><span style="font-size:0.74rem;">(belum terhubung ke master lokasi)</span></span>
                @endif
            </div>
        </div>

        {{-- Foto Barang --}}
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
                Foto Barang
            </div>
            <div class="form-card-body" style="align-items:center;">
                @if($barang->foto)
                    <img src="{{ asset('storage/' . $barang->foto) }}"
                         alt="{{ $barang->nama_barang }}"
                         style="width:100%;max-height:260px;object-fit:cover;border-radius:10px;border:1px solid var(--border);">
                @else
                    <div class="foto-placeholder" style="background:#f8fafc;border-radius:10px;border:1px solid var(--border);width:100%;min-height:160px;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#94a3b8;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:30px;height:30px;margin-bottom:8px;">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                        <p style="margin:0;font-size:0.8rem;">Tidak ada foto</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- QR Code --}}
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="3" height="3"/>
                </svg>
                QR Code
            </div>
            <div class="form-card-body" style="align-items:center;text-align:center;gap:12px;">
                @if($barang->qrCode)
                    <img src="{{ asset('storage/' . $barang->qrCode->qr_code_path) }}"
                         alt="QR {{ $barang->kode_barang }}"
                         style="width:160px;height:160px;object-fit:contain;background:#f8fafc;padding:8px;border-radius:10px;border:1px solid var(--border);">
                    <div style="display:flex;gap:8px;width:100%;">
                        <a href="{{ route('admin.qrcode.cetak', $barang->qrCode->id) }}"
                           target="_blank" class="btn-primary btn-sm" style="flex:1;justify-content:center;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 6 2 18 2 18 9"/>
                                <path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/>
                                <rect x="6" y="14" width="12" height="8"/>
                            </svg>
                            Cetak
                        </a>
                        <a href="{{ route('admin.qrcode.download', $barang->qrCode->id) }}"
                           class="btn-secondary btn-sm" style="flex:1;justify-content:center;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            Download
                        </a>
                    </div>
                @else
                    <div class="qr-empty-placeholder" style="padding:20px 0;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="width:80px;height:80px;color:#cbd5e1;">
                            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                            <rect x="3" y="14" width="7" height="7"/>
                        </svg>
                        <p style="font-size:0.8rem;color:var(--text-muted);margin:8px 0 12px;">Belum ada QR Code</p>
                    </div>
                    <a href="{{ route('admin.qrcode.index') }}" class="btn-primary btn-sm btn-full">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                            <rect x="3" y="14" width="7" height="7"/>
                        </svg>
                        Generate QR Code
                    </a>
                @endif
            </div>
        </div>

        {{-- Hapus Barang --}}
        <div class="form-card" style="border-color:#fee2e2;">
            <div class="form-card-header" style="background:#fff5f5;color:#ef4444;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                Zona Berbahaya
            </div>
            <div class="form-card-body">
                <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:12px;">
                    Menghapus barang akan menghapus semua data terkait termasuk QR Code dan riwayat pengecekan secara permanen.
                </p>
                <form method="POST" action="{{ route('admin.barang.destroy', $barang->id) }}"
                      onsubmit="return confirm('Yakin hapus barang {{ $barang->kode_barang }}?\nSemua data terkait akan ikut terhapus!')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-full" style="display:flex;align-items:center;justify-content:center;gap:8px;padding:10px;background:#fef2f2;color:#ef4444;border:1.5px solid #fecaca;border-radius:10px;font-size:0.875rem;font-weight:600;font-family:inherit;cursor:pointer;transition:background 0.15s;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                            <path d="M10 11v6M14 11v6"/>
                        </svg>
                        Hapus Barang Ini
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
.show-grid {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 20px;
    align-items: start;
}
.show-col-main { display: flex; flex-direction: column; gap: 20px; }
.show-col-side  { display: flex; flex-direction: column; gap: 20px; }

.detail-table { width: 100%; border-collapse: collapse; }
.detail-table tr { border-bottom: 1px solid var(--border); }
.detail-table tr:last-child { border-bottom: none; }
.detail-label {
    padding: 12px 20px;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-muted);
    width: 145px;
    white-space: nowrap;
    background: #fafafa;
}
.detail-val {
    padding: 12px 20px;
    font-size: 0.875rem;
    color: var(--text-main);
    font-weight: 500;
}

/* CSS TIMELINE */
.timeline-container {
    border-left: 2px solid #e2e8f0;
    padding-left: 20px;
    margin-left: 10px;
    position: relative;
}
.timeline-item {
    margin-bottom: 20px;
    position: relative;
}
.timeline-item:last-child {
    margin-bottom: 0;
}
.timeline-item::before {
    content: '';
    position: absolute;
    left: -26.5px;
    top: 4px;
    width: 11px;
    height: 11px;
    border-radius: 50%;
    background: #2563eb;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #2563eb;
}
.timeline-date {
    font-size: 0.72rem;
    color: #64748b;
    font-weight: 700;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.timeline-content {
    background: #f8fafc;
    padding: 12px 16px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

@media (max-width: 1024px) {
    .show-grid { grid-template-columns: 1fr; }
    .show-col-side { order: -1; }
}
</style>
@endpush