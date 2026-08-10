@extends('layouts.admin')
@section('title', 'Edit Jadwal')
@section('page-title', 'Edit Jadwal')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-sep">/</span>
            <a href="{{ route('admin.jadwal.index') }}" class="breadcrumb-link">Jadwal Pengecekan</a>
            <span class="breadcrumb-sep">/</span>
            <span class="breadcrumb-current">Edit Jadwal</span>
        </nav>
        <h2 class="page-heading">Edit <span class="heading-accent">Jadwal</span></h2>
    </div>
    <a href="{{ route('admin.jadwal.index') }}" class="btn-secondary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
        </svg>
        Kembali
    </a>
</div>

<div class="form-grid">
    <div class="form-col-main">
        <form method="POST" action="{{ route('admin.jadwal.update', $jadwal->id) }}">
            @csrf @method('PUT')
            <div class="form-card">
                <div class="form-card-header">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Detail Jadwal & Penugasan
                </div>
                <div class="form-card-body">

                    <div class="form-group">
                        <label class="form-label required">Judul Jadwal</label>
                        <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror"
                               value="{{ old('judul', $jadwal->judul) }}">
                        @error('judul')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Barang</label>
                        <select name="barang_id" class="form-control">
                            @foreach($barang_list as $b)
                                <option value="{{ $b->id }}" {{ old('barang_id',$jadwal->barang_id)==$b->id ? 'selected':'' }}>
                                    {{ $b->kode_barang }} - {{ $b->nama_barang }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Form kembali jadi standar agar tidak mencolok --}}
                    <div class="form-group">
                        <label class="form-label required">Tugaskan Kepada (Petugas)</label>
                        <select name="user_id" class="form-control @error('user_id') is-invalid @enderror">
                            <option value="">-- Pilih Petugas yang Mengerjakan --</option>
                            @foreach($user_list as $u)
                                @php 
                                    $spesialis = $kategori_list->firstWhere('id', $u->spesialisasi_id); 
                                @endphp
                                <option value="{{ $u->id }}" {{ old('user_id', $jadwal->user_id) == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} (Spesialisasi: {{ $spesialis ? $spesialis->nama : 'Umum' }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label required">Tanggal Jadwal</label>
                            <input type="date" name="tanggal_jadwal" class="form-control"
                                   value="{{ old('tanggal_jadwal', \Carbon\Carbon::parse($jadwal->tanggal_jadwal)->format('Y-m-d')) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Frekuensi</label>
                            <select name="frekuensi" class="form-control">
                                <option value="bulanan"  {{ old('frekuensi',$jadwal->frekuensi)==='bulanan'  ? 'selected':'' }}>Bulanan</option>
                                <option value="mingguan" {{ old('frekuensi',$jadwal->frekuensi)==='mingguan' ? 'selected':'' }}>Mingguan</option>
                                <option value="harian"   {{ old('frekuensi',$jadwal->frekuensi)==='harian'   ? 'selected':'' }}>Harian</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Status</label>
                        <select name="status" class="form-control">
                            <option value="pending"   {{ old('status',$jadwal->status)==='pending'   ? 'selected':'' }}>Aktif (Pending)</option>
                            <option value="selesai"   {{ old('status',$jadwal->status)==='selesai'   ? 'selected':'' }}>Selesai</option>
                            <option value="terlambat" {{ old('status',$jadwal->status)==='terlambat' ? 'selected':'' }}>Terlambat</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control form-textarea" rows="3">{{ old('keterangan', $jadwal->keterangan) }}</textarea>
                    </div>

                    <div class="form-actions" style="border-top:1px solid var(--border);padding-top:16px;">
                        <button type="submit" class="btn-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v14a2 2 0 01-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                            </svg>
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.jadwal.index') }}" class="btn-secondary">Batal</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <div class="form-col-side">
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="3" width="20" height="14" rx="2"/>
                </svg>
                Info Barang
            </div>
            <div class="form-card-body" style="gap:8px;">
                <div style="font-size:0.8rem;color:var(--text-muted);">Kode</div>
                <div style="font-weight:700;color:#2563eb;font-family:monospace;">{{ $jadwal->barang->kode_barang }}</div>
                <div style="font-size:0.8rem;color:var(--text-muted);margin-top:8px;">Lokasi</div>
                <div style="font-size:0.875rem;font-weight:600;">{{ $jadwal->barang->lokasi }}</div>
            </div>
        </div>
    </div>
</div>
@endsection