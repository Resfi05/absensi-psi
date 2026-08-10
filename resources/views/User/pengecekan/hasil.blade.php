@extends('layouts.user')
@section('title', 'Hasil Pengecekan')
@section('content')

<div style="text-align:center;padding:30px 10px 20px;">
    <div id="successIcon" style="width:84px;height:84px;border-radius:50%;background:{{ $pengecekan->status === 'aman' ? 'var(--p-success-lt)' : 'var(--p-warning-lt)' }};display:flex;align-items:center;justify-content:center;margin:0 auto 18px;animation:popIn 0.4s ease;">
        <svg viewBox="0 0 24 24" fill="none" stroke="{{ $pengecekan->status === 'aman' ? 'var(--p-success)' : 'var(--p-warning)' }}" stroke-width="2" style="width:42px;height:42px;">
            @if($pengecekan->status === 'aman')
                <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            @else
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
            @endif
        </svg>
    </div>
    <h2 style="font-size:1.15rem;font-weight:800;color:var(--p-text);margin-bottom:6px;">Pengecekan Berhasil Disimpan!</h2>
    <p style="font-size:0.85rem;color:var(--p-muted);">{{ $pengecekan->jadwal->barang->nama_barang }}</p>
</div>

<div class="progress-card">
    <div style="display:flex;flex-direction:column;gap:14px;">
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:0.8rem;color:var(--p-muted);">Status</span>
            <span class="task-status {{ $pengecekan->status === 'aman' ? 'badge-aman' : 'badge-rusak' }}">
                {{ $pengecekan->status === 'aman' ? 'Aman' : 'Perlu Tindakan' }}
            </span>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:0.8rem;color:var(--p-muted);">Lokasi</span>
            <span style="font-size:0.82rem;font-weight:600;color:var(--p-text);">{{ $pengecekan->jadwal->barang->lokasiRelasi->nama ?? '—' }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:0.8rem;color:var(--p-muted);">Waktu Submit</span>
            <span style="font-size:0.82rem;font-weight:600;color:var(--p-text);">{{ $pengecekan->checked_at->translatedFormat('d M Y, H:i') }}</span>
        </div>
        @if($pengecekan->notes)
        <div style="background:var(--p-warning-lt);border-radius:10px;padding:12px;">
            <div style="font-size:0.74rem;font-weight:700;color:var(--p-warning);margin-bottom:4px;">Catatan</div>
            <div style="font-size:0.8rem;color:var(--p-text);line-height:1.5;">{{ $pengecekan->notes }}</div>
        </div>
        @endif
    </div>
</div>

@if($pengecekan->status === 'perlu_tindakan')
<div class="tip-banner" style="background:var(--p-warning-lt);margin-bottom:18px;">
    <div class="tip-icon" style="background:rgba(255,255,255,0.7);">📋</div>
    <div>
        <div class="tip-title" style="color:#92400e;">Laporan diteruskan ke Admin</div>
        <div class="tip-sub" style="color:#b45309;">Temuan Anda akan direview dan ditindaklanjuti oleh Admin.</div>
    </div>
</div>
@endif

<a href="{{ route('user.jadwal.index') }}" class="btn-block btn-primary-block" style="margin-bottom:10px;">
    Kembali ke Jadwal
</a>
<a href="{{ route('user.dashboard') }}" class="btn-block btn-outline-block">
    Ke Dashboard
</a>

@endsection

@push('styles')
<style>
@keyframes popIn {
    0% { transform: scale(0.6); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}
</style>
@endpush