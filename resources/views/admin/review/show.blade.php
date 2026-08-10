@extends('layouts.admin')
@section('title', 'Detail Temuan')
@section('page-title', 'Detail Temuan')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-sep">›</span>
            <a href="{{ route('admin.review.index') }}" class="breadcrumb-link">Review Hasil</a>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-current">Detail Temuan</span>
        </nav>
        <h2 class="page-heading">Detail <span class="heading-accent">Temuan Masalah</span></h2>
    </div>
    <a href="{{ route('admin.review.index') }}" class="btn-secondary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
        </svg>
        Kembali
    </a>
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

{{-- Status Hero --}}
<div class="rev-detail-hero" style="background:{{ $pengecekan->statusTindakLanjutColor() }}10;border:1px solid {{ $pengecekan->statusTindakLanjutColor() }}30;">
    <div class="rev-detail-hero-icon" style="background:{{ $pengecekan->statusTindakLanjutColor() }}20;color:{{ $pengecekan->statusTindakLanjutColor() }};">
        @if($pengecekan->status_tindak_lanjut === 'selesai')
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        @elseif($pengecekan->status_tindak_lanjut === 'ditangani')
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
        @elseif($pengecekan->status_tindak_lanjut === 'diabaikan')
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        @else
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        @endif
    </div>
    <div>
        <div style="font-size:1.1rem;font-weight:800;color:{{ $pengecekan->statusTindakLanjutColor() }};">{{ $pengecekan->statusTindakLanjutLabel() }}</div>
        <div style="font-size:0.82rem;color:var(--text-muted);margin-top:2px;">
            {{ $pengecekan->jadwal->barang->nama_barang ?? '—' }}
            @if($pengecekan->checked_at)
                — Dilaporkan {{ \Carbon\Carbon::parse($pengecekan->checked_at)->translatedFormat('d F Y, H:i') }}
            @endif
        </div>
    </div>
</div>

<div class="show-grid">
    <div class="show-col-main">

        {{-- Jenis Kerusakan / Catatan Petugas --}}
        <div class="form-card" style="border-color:#fecaca;">
            <div class="form-card-header" style="background:#fff5f5;color:#ef4444;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                Catatan Temuan dari Petugas
            </div>
            <div style="padding:20px;">
                @if($pengecekan->notes)
                    <p style="font-size:0.95rem;color:var(--text-main);line-height:1.7;font-weight:500;">{{ $pengecekan->notes }}</p>
                @else
                    <p style="font-size:0.85rem;color:var(--text-muted);">Petugas tidak meninggalkan catatan khusus.</p>
                @endif
            </div>
        </div>

        {{-- Checklist (kontekstual) --}}
        @if($pengecekan->checklist_data)
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                </svg>
                Checklist Pengecekan
            </div>
            <div style="padding:18px;">
                <div style="display:flex;flex-direction:column;gap:8px;">
                    @foreach($pengecekan->checklist_data as $item)
                    <div style="display:flex;align-items:center;gap:10px;padding:9px 14px;background:{{ $item['checked'] ? '#f0fdf4' : '#fef2f2' }};border-radius:8px;border:1px solid {{ $item['checked'] ? '#bbf7d0' : '#fecaca' }};">
                        <div style="width:20px;height:20px;border-radius:50%;background:{{ $item['checked'] ? '#16a34a' : '#ef4444' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            @if($item['checked'])
                                <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" style="width:11px;height:11px;"><polyline points="20 6 9 17 4 12"/></svg>
                            @else
                                <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" style="width:9px;height:9px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            @endif
                        </div>
                        <span style="font-size:0.82rem;color:var(--text-main);">{{ $item['item'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Foto Bukti --}}
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
                Foto Bukti Kerusakan
            </div>
            <div style="padding:18px;">
                @if($pengecekan->photo_before)
                    <img src="{{ asset('storage/' . $pengecekan->photo_before) }}" style="width:100%;max-height:320px;object-fit:cover;border-radius:10px;border:1px solid var(--border);">
                @else
                    <div style="width:100%;height:200px;background:#f1f5f9;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#cbd5e1;border:1px dashed var(--border);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:36px;height:36px;"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
                    </div>
                @endif
            </div>
        </div>

        {{-- Riwayat Temuan Lain --}}
        @if($riwayatLain->count() > 0)
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                Riwayat Temuan Lain untuk Barang Ini
            </div>
            <div style="padding:8px 12px;">
                @foreach($riwayatLain as $r)
                <a href="{{ route('admin.review.show', $r->id) }}" style="display:flex;align-items:center;justify-content:space-between;padding:10px 8px;border-bottom:1px solid var(--border);text-decoration:none;">
                    <div>
                        <div style="font-size:0.8rem;color:var(--text-main);">{{ $r->notes ? \Illuminate\Support\Str::limit($r->notes, 50) : 'Tidak ada catatan' }}</div>
                        <div style="font-size:0.7rem;color:var(--text-muted);">{{ $r->checked_at ? \Carbon\Carbon::parse($r->checked_at)->translatedFormat('d M Y') : '' }}</div>
                    </div>
                    <span class="badge-status" style="background:{{ $r->statusTindakLanjutColor() }}20;color:{{ $r->statusTindakLanjutColor() }};font-size:0.66rem;">
                        {{ $r->statusTindakLanjutLabel() }}
                    </span>
                </a>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    <div class="show-col-side">

        {{-- Info Barang --}}
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="3" width="20" height="14" rx="2"/>
                    <line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
                </svg>
                Info Barang
            </div>
            <div class="form-card-body" style="gap:8px;">
                <div style="font-size:0.75rem;color:var(--text-muted);">Kode Barang</div>
                <div style="font-family:monospace;font-size:0.875rem;font-weight:700;color:#2563eb;">{{ $pengecekan->jadwal->barang->kode_barang ?? '—' }}</div>
                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px;">Nama Barang</div>
                <div style="font-size:0.875rem;font-weight:600;">{{ $pengecekan->jadwal->barang->nama_barang ?? '—' }}</div>
                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px;">Kategori</div>
                <div>
                    @if($pengecekan->jadwal->barang->kategori ?? null)
                        <span class="badge-jenis" style="background:{{ $pengecekan->jadwal->barang->kategori->warna }}20;color:{{ $pengecekan->jadwal->barang->kategori->warna }};border:1px solid {{ $pengecekan->jadwal->barang->kategori->warna }}40;">
                            {{ $pengecekan->jadwal->barang->kategori->nama }}
                        </span>
                    @endif
                </div>
                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px;">Lokasi</div>
                <div style="font-size:0.875rem;font-weight:600;">📍 {{ $pengecekan->jadwal->barang->lokasiRelasi->nama ?? '—' }}</div>
                <div style="margin-top:8px;">
                    <a href="{{ route('admin.barang.show', $pengecekan->jadwal->barang->id) }}" class="btn-secondary btn-sm btn-full">Lihat Detail Barang</a>
                </div>
            </div>
        </div>

        {{-- Info Petugas --}}
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
                Dilaporkan Oleh
            </div>
            <div class="form-card-body" style="align-items:center;text-align:center;gap:8px;">
                <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#1e40af,#3b82f6);color:white;display:flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:700;">
                    {{ strtoupper(substr($pengecekan->user->name ?? '?',0,1)) }}
                </div>
                <div style="font-weight:700;color:var(--text-main);">{{ $pengecekan->user->name ?? '—' }}</div>
                <div style="font-size:0.78rem;color:var(--text-muted);">{{ $pengecekan->user->no_hp ?? '' }}</div>
            </div>
        </div>

        {{-- Panel Aksi --}}
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                </svg>
                Tindak Lanjut
            </div>
            <div class="form-card-body" style="gap:10px;">

                @if($pengecekan->status_tindak_lanjut === 'menunggu')
                    {{-- Tombol Tindak Lanjuti --}}
                    <button onclick="document.getElementById('modalTindakLanjut').classList.add('active')"
                            class="btn-primary btn-full" style="justify-content:center;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        Buat Jadwal Perbaikan
                    </button>

                    {{-- Tombol Abaikan --}}
                    <form method="POST" action="{{ route('admin.review.abaikan', $pengecekan->id) }}"
                          onsubmit="return confirm('Tandai temuan ini sebagai false alarm / diabaikan?')">
                        @csrf
                        <button type="submit" class="btn-full" style="display:flex;align-items:center;justify-content:center;gap:8px;padding:10px;background:#f8fafc;color:#64748b;border:1.5px solid var(--border);border-radius:10px;font-size:0.875rem;font-weight:600;font-family:inherit;cursor:pointer;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;">
                                <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                            </svg>
                            Abaikan / False Alarm
                        </button>
                    </form>

                @elseif($pengecekan->status_tindak_lanjut === 'ditangani')
                    <div style="background:#eff6ff;border-radius:8px;padding:12px;font-size:0.8rem;color:#2563eb;text-align:center;">
                        🔧 Jadwal perbaikan sudah dibuat dan menunggu dikerjakan teknisi.
                    </div>
                    <form method="POST" action="{{ route('admin.review.selesaikan', $pengecekan->id) }}"
                          onsubmit="return confirm('Tandai temuan ini sudah selesai diperbaiki? Pastikan sudah diverifikasi.')">
                        @csrf
                        <button type="submit" class="btn-primary btn-full" style="background:#16a34a;box-shadow:0 2px 8px rgba(22,163,74,0.3);justify-content:center;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            Tandai Selesai Diperbaiki
                        </button>
                    </form>

                @elseif($pengecekan->status_tindak_lanjut === 'selesai')
                    <div style="background:#f0fdf4;border-radius:8px;padding:14px;font-size:0.82rem;color:#16a34a;text-align:center;font-weight:600;">
                        ✅ Temuan ini sudah selesai diperbaiki dan diverifikasi.
                    </div>

                @elseif($pengecekan->status_tindak_lanjut === 'diabaikan')
                    <div style="background:#f8fafc;border-radius:8px;padding:14px;font-size:0.82rem;color:#64748b;text-align:center;">
                        ⊘ Temuan ini telah diabaikan (dianggap false alarm).
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>

{{-- Modal Tindak Lanjut --}}
<div class="modal-overlay" id="modalTindakLanjut" onclick="this.classList.remove('active')">
    <div class="modal-box" style="max-width:420px" onclick="event.stopPropagation()">
        <div class="modal-header">
            <div>
                <div class="modal-title" style="font-family:inherit;">Buat Jadwal Perbaikan</div>
                <div class="modal-sub">Tugas ini akan muncul di HP teknisi yang sesuai</div>
            </div>
            <button class="modal-close" onclick="document.getElementById('modalTindakLanjut').classList.remove('active')">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.review.tindak-lanjuti', $pengecekan->id) }}">
            @csrf
            <div class="modal-body" style="flex-direction:column;gap:14px;align-items:stretch;">
                <div class="form-group">
                    <label class="form-label required">Tanggal Perbaikan</label>
                    <input type="date" name="tanggal_perbaikan" class="form-control"
                           value="{{ now()->format('Y-m-d') }}" min="{{ now()->format('Y-m-d') }}" required>
                    <span class="form-hint">Kapan teknisi harus datang memperbaiki barang ini</span>
                </div>
                <div style="background:#eff6ff;border-radius:8px;padding:10px 12px;font-size:0.78rem;color:#2563eb;">
                    💡 Jadwal perbaikan otomatis ditugaskan ke petugas dengan spesialisasi
                    <strong>{{ $pengecekan->jadwal->barang->kategori->nama ?? '—' }}</strong>.
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-primary" style="flex:1;justify-content:center;">Buat Jadwal</button>
                <button type="button" class="btn-secondary"
                        onclick="document.getElementById('modalTindakLanjut').classList.remove('active')">Batal</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
.rev-detail-hero {
    border-radius: 14px;
    padding: 20px 24px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
}
.rev-detail-hero-icon {
    width: 50px; height: 50px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.rev-detail-hero-icon svg { width: 24px; height: 24px; }
</style>
@endpush

@push('scripts')
<script>
setTimeout(() => {
    const a = document.getElementById('flashAlert');
    if (a) a.style.opacity = '0', setTimeout(() => a.remove(), 400);
}, 5000);
</script>
@endpush