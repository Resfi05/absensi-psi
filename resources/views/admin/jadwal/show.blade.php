@extends('layouts.admin')
@section('title', 'Detail Jadwal')
@section('page-title', 'Detail Jadwal')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-sep">›</span>
            <a href="{{ route('admin.jadwal.index') }}" class="breadcrumb-link">Jadwal Pengecekan</a>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-current">Detail Jadwal</span>
        </nav>
        <h2 class="page-heading">Detail <span class="heading-accent">Jadwal Pengecekan</span></h2>
    </div>
    <div style="display:flex;gap:10px;">
        @if($jadwal->status === 'pending')
        <form method="POST" action="{{ route('admin.jadwal.selesai', $jadwal->id) }}"
              onsubmit="return confirm('Tandai jadwal ini selesai? Jadwal berikutnya akan otomatis dibuat.')">
            @csrf
            <button type="submit" class="btn-primary" style="background:#16a34a;box-shadow:0 2px 8px rgba(22,163,74,0.3)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                Tandai Selesai
            </button>
        </form>
        @endif
        <a href="{{ route('admin.jadwal.index') }}" class="btn-secondary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
            </svg>
            Kembali
        </a>
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

<div class="show-grid">

    {{-- ── KOLOM KIRI ──────────────────────────────── --}}
    <div class="show-col-main">

        {{-- Info Jadwal --}}
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                Informasi Jadwal
                {{-- Edit inline --}}
                <button onclick="toggleEdit()" id="btnEdit"
                        style="margin-left:auto;display:flex;align-items:center;gap:6px;padding:6px 14px;background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;border-radius:8px;font-size:0.8rem;font-weight:600;font-family:inherit;cursor:pointer;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px">
                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Edit
                </button>
            </div>
            <div class="form-card-body" style="gap:0;padding:0;" id="viewMode">
                <table class="detail-table">
                    <tr>
                        <td class="detail-label">Barang</td>
                        <td class="detail-val">
                            <span style="font-family:monospace;font-size:0.82rem;font-weight:700;color:#2563eb;background:#eff6ff;padding:2px 8px;border-radius:5px;">
                                {{ $jadwal->barang->kode_barang }}
                            </span>
                            <span style="margin-left:8px;font-weight:600;">{{ $jadwal->barang->nama_barang }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="detail-label">Jenis</td>
                        <td class="detail-val">
                            <span class="badge-jenis {{ $jadwal->barang->jenis_barang === 'AC' ? 'badge-blue' : 'badge-red' }}">
                                {{ $jadwal->barang->jenis_barang }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="detail-label">Lokasi</td>
                        <td class="detail-val">
                            @if($jadwal->barang->lokasiRelasi)
                                📍 {{ $jadwal->barang->lokasiRelasi->nama }}
                            @else
                                <span style="color:#d97706;">⚠️ Belum ada lokasi</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="detail-label">Tanggal Mulai</td>
                        <td class="detail-val">{{ $jadwal->tanggal_mulai ? \Carbon\Carbon::parse($jadwal->tanggal_mulai)->translatedFormat('d F Y') : '—' }}</td>
                    </tr>
                    <tr>
                        <td class="detail-label">Jadwal Ini</td>
                        <td class="detail-val">
                            <strong>{{ \Carbon\Carbon::parse($jadwal->tanggal_jadwal)->translatedFormat('d F Y') }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="detail-label">Jadwal Berikutnya</td>
                        <td class="detail-val">
                            <span style="color:#16a34a;font-weight:600;">
                                {{ $jadwal_berikutnya->translatedFormat('d F Y') }}
                            </span>
                            <span style="font-size:0.75rem;color:var(--text-muted);margin-left:6px;">(otomatis)</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="detail-label">Frekuensi</td>
                        <td class="detail-val">
                            <span class="jadwal-frek-badge">{{ $jadwal->frekuensiLabel() }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="detail-label">Status</td>
                        <td class="detail-val">
                            @if($jadwal->status === 'selesai')
                                <span class="badge-status badge-green">Selesai</span>
                            @elseif($jadwal->status === 'terlambat')
                                <span class="badge-status badge-red">Terlambat</span>
                            @else
                                <span class="badge-status badge-orange">Aktif</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="detail-label">Keterangan</td>
                        <td class="detail-val">{{ $jadwal->keterangan ?? '—' }}</td>
                    </tr>
                </table>
            </div>

            {{-- Form Edit (tersembunyi) --}}
            <div id="editMode" style="display:none;padding:20px;">
                <form method="POST" action="{{ route('admin.jadwal.update', $jadwal->id) }}">
                    @csrf @method('PUT')
                    <div style="display:flex;flex-direction:column;gap:16px;">

                        <div class="form-group">
                            <label class="form-label required">Barang</label>
                            <select name="barang_id" class="form-control">
                                @foreach(\App\Models\Barang::where('is_active',true)->whereNotNull('lokasi_id')->whereNotNull('kategori_id')->orderBy('kode_barang')->get() as $b)
                                    <option value="{{ $b->id }}" {{ $jadwal->barang_id == $b->id ? 'selected':'' }}>
                                        {{ $b->kode_barang }} — {{ $b->nama_barang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div class="form-group">
                                <label class="form-label required">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" class="form-control"
                                       value="{{ $jadwal->tanggal_mulai ? \Carbon\Carbon::parse($jadwal->tanggal_mulai)->format('Y-m-d') : '' }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Tanggal Jadwal Ini</label>
                                <input type="date" name="tanggal_jadwal" class="form-control"
                                       value="{{ \Carbon\Carbon::parse($jadwal->tanggal_jadwal)->format('Y-m-d') }}">
                            </div>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div class="form-group">
                                <label class="form-label required">Frekuensi</label>
                                <select name="frekuensi" class="form-control">
                                    <option value="bulanan"  {{ $jadwal->frekuensi==='bulanan'  ? 'selected':'' }}>Setiap Bulan</option>
                                    <option value="mingguan" {{ $jadwal->frekuensi==='mingguan' ? 'selected':'' }}>Setiap Minggu</option>
                                    <option value="harian"   {{ $jadwal->frekuensi==='harian'   ? 'selected':'' }}>Setiap Hari</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Status</label>
                                <select name="status" class="form-control">
                                    <option value="pending"   {{ $jadwal->status==='pending'   ? 'selected':'' }}>Aktif</option>
                                    <option value="selesai"   {{ $jadwal->status==='selesai'   ? 'selected':'' }}>Selesai</option>
                                    <option value="terlambat" {{ $jadwal->status==='terlambat' ? 'selected':'' }}>Terlambat</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control form-textarea" rows="2">{{ $jadwal->keterangan }}</textarea>
                        </div>

                        <div style="display:flex;gap:10px;">
                            <button type="submit" class="btn-primary btn-sm">Simpan Perubahan</button>
                            <button type="button" onclick="toggleEdit()" class="btn-secondary btn-sm">Batal</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Riwayat Pengecekan --}}
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                </svg>
                Riwayat Pengecekan Barang Ini
            </div>
            <div class="table-wrap">
                @if($jadwal->pengecekan->count() > 0)
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
                        @foreach($jadwal->pengecekan as $i => $p)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $p->user->name ?? '—' }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal_pengecekan)->translatedFormat('d M Y, H:i') }}</td>
                            <td><span class="badge-status {{ $p->status === 'selesai' ? 'badge-green' : 'badge-orange' }}">{{ ucfirst($p->status) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-table-state" style="padding:40px 20px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:48px;height:48px;opacity:0.3;">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                    </svg>
                    <p>Belum ada riwayat pengecekan</p>
                </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ── KOLOM KANAN ─────────────────────────────── --}}
    <div class="show-col-side">

        {{-- Status Card --}}
        <div class="form-card" style="border-top:3px solid {{ $jadwal->status === 'selesai' ? '#22c55e' : ($jadwal->status === 'terlambat' ? '#ef4444' : '#f59e0b') }}">
            <div class="form-card-body" style="align-items:center;text-align:center;gap:12px;">
                @php
                    $tgl = \Carbon\Carbon::parse($jadwal->tanggal_jadwal);
                    $diff = $tgl->diffForHumans();
                @endphp
                <div style="font-size:2.5rem;line-height:1;">
                    @if($jadwal->status === 'selesai') ✅
                    @elseif($jadwal->status === 'terlambat') ⚠️
                    @else 📅
                    @endif
                </div>
                <div style="font-size:1.5rem;font-weight:800;color:var(--text-main);">
                    {{ $tgl->translatedFormat('d F') }}
                </div>
                <div style="font-size:0.875rem;color:var(--text-muted);">{{ $tgl->format('Y') }}</div>
                <div style="font-size:0.8rem;color:{{ $jadwal->status === 'selesai' ? '#16a34a' : ($jadwal->isTerlambat() ? '#ef4444' : '#d97706') }};font-weight:600;">
                    {{ $diff }}
                </div>
                @if($jadwal->status === 'pending')
                <div style="width:100%;background:#f1f5f9;border-radius:8px;padding:10px;font-size:0.78rem;color:var(--text-muted);">
                    Jadwal berikutnya:<br>
                    <strong style="color:var(--text-main);">{{ $jadwal_berikutnya->translatedFormat('d F Y') }}</strong>
                </div>
                @endif
            </div>
        </div>

        {{-- Info Barang --}}
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="3" width="20" height="14" rx="2"/>
                    <line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
                </svg>
                Info Barang
            </div>
            <div class="form-card-body" style="gap:10px;">
                @if($jadwal->barang->foto)
                    <img src="{{ asset('storage/' . $jadwal->barang->foto) }}"
                         style="width:100%;height:140px;object-fit:cover;border-radius:8px;border:1px solid var(--border);">
                @endif
                <div style="font-size:0.75rem;color:var(--text-muted);">Kode Barang</div>
                <div style="font-family:monospace;font-size:0.875rem;font-weight:700;color:#2563eb;">{{ $jadwal->barang->kode_barang }}</div>
                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px;">Kategori</div>
                <div>
                    @if($jadwal->barang->kategori)
                        <span class="badge-jenis" style="background:{{ $jadwal->barang->kategori->warna }}20;color:{{ $jadwal->barang->kategori->warna }};border:1px solid {{ $jadwal->barang->kategori->warna }}40;">
                            {{ $jadwal->barang->kategori->nama }}
                        </span>
                    @else
                        <span style="font-size:0.82rem;color:var(--text-muted);">—</span>
                    @endif
                </div>
                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px;">Lokasi</div>
                <div style="font-size:0.875rem;font-weight:600;">
                    @if($jadwal->barang->lokasiRelasi)
                        📍 {{ $jadwal->barang->lokasiRelasi->nama }}
                    @else
                        <span style="color:#d97706;">⚠️ Belum ada lokasi</span>
                    @endif
                </div>
                @if($jadwal->barang->qrCode)
                <div style="margin-top:8px;">
                    <a href="{{ route('admin.qrcode.cetak', $jadwal->barang->qrCode->id) }}"
                       target="_blank" class="btn-secondary btn-sm btn-full">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                            <rect x="3" y="14" width="7" height="7"/>
                        </svg>
                        Lihat QR Code
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Petugas yang Sesuai (akan dapat tugas ini di HP) --}}
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
                </svg>
                Petugas yang Sesuai
            </div>
            <div style="padding:14px;">
                @if($petugas_sesuai->count() > 0)
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        @foreach($petugas_sesuai as $p)
                        <div style="display:flex;align-items:center;gap:10px;padding:8px 10px;background:#f8fafc;border-radius:8px;">
                            <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#1e40af,#3b82f6);color:white;display:flex;align-items:center;justify-content:center;font-size:0.78rem;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr($p->name,0,1)) }}
                            </div>
                            <div>
                                <div style="font-size:0.82rem;font-weight:600;color:var(--text-main);">{{ $p->name }}</div>
                                <div style="font-size:0.7rem;color:var(--text-muted);">{{ $p->no_hp ?? 'Tidak ada No. HP' }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div style="font-size:0.72rem;color:var(--text-muted);margin-top:10px;">
                        Tugas ini akan otomatis muncul di HP petugas di atas saat mereka login.
                    </div>
                @else
                    <div style="text-align:center;padding:10px 0;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:36px;height:36px;color:#cbd5e1;margin-bottom:6px;">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        </svg>
                        <p style="font-size:0.78rem;color:#d97706;">Belum ada petugas dengan spesialisasi yang cocok</p>
                        <a href="{{ route('admin.users.create') }}" style="font-size:0.74rem;color:#2563eb;text-decoration:underline;">+ Tambah petugas</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Barang Lain di Lokasi yang Sama (preview pengelompokan) --}}
        @if($jadwal->barang->lokasiRelasi)
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                Barang Lain di Lokasi Ini
            </div>
            <div style="padding:14px;">
                @if($barang_satu_lokasi->count() > 0)
                    <div style="display:flex;flex-direction:column;gap:6px;">
                        @foreach($barang_satu_lokasi as $bl)
                        <a href="{{ route('admin.barang.show', $bl->id) }}" style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;background:#f8fafc;border-radius:8px;text-decoration:none;">
                            <div>
                                <div style="font-size:0.8rem;font-weight:600;color:var(--text-main);">{{ $bl->nama_barang }}</div>
                                <div style="font-family:monospace;font-size:0.68rem;color:#2563eb;">{{ $bl->kode_barang }}</div>
                            </div>
                            @if($bl->kategori)
                                <span class="badge-jenis" style="background:{{ $bl->kategori->warna }}20;color:{{ $bl->kategori->warna }};border:1px solid {{ $bl->kategori->warna }}40;font-size:0.66rem;">
                                    {{ $bl->kategori->nama }}
                                </span>
                            @endif
                        </a>
                        @endforeach
                    </div>
                    <div style="font-size:0.72rem;color:var(--text-muted);margin-top:10px;">
                        💡 Di HP petugas, barang-barang di ruangan ini akan ditampilkan sebagai satu paket tugas.
                    </div>
                @else
                    <p style="font-size:0.78rem;color:var(--text-muted);text-align:center;padding:8px 0;">
                        Tidak ada barang lain di lokasi ini.
                    </p>
                @endif
            </div>
        </div>
        @endif

        {{-- Riwayat Jadwal Barang --}}
        <div class="form-card">
            <div class="form-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                Riwayat Jadwal
            </div>
            <div style="padding:8px 12px;max-height:240px;overflow-y:auto;">
                @foreach($riwayat as $r)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 8px;border-bottom:1px solid var(--border);{{ $r->id === $jadwal->id ? 'background:#eff6ff;border-radius:6px;' : '' }}">
                    <div>
                        <div style="font-size:0.8rem;font-weight:600;color:{{ $r->id === $jadwal->id ? '#2563eb' : 'var(--text-main)' }};">
                            {{ \Carbon\Carbon::parse($r->tanggal_jadwal)->translatedFormat('d M Y') }}
                            @if($r->id === $jadwal->id) <span style="font-size:0.7rem;background:#dbeafe;padding:1px 6px;border-radius:10px;">Ini</span> @endif
                        </div>
                        <div style="font-size:0.72rem;color:var(--text-muted);">{{ $r->frekuensiLabel() }}</div>
                    </div>
                    <span class="badge-status {{ $r->status === 'selesai' ? 'badge-green' : ($r->status === 'terlambat' ? 'badge-red' : 'badge-orange') }}" style="font-size:0.68rem;">
                        {{ ucfirst($r->status) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Hapus --}}
        <div class="form-card" style="border-color:#fee2e2;">
            <div class="form-card-header" style="background:#fff5f5;color:#ef4444;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                Hapus Jadwal
            </div>
            <div class="form-card-body">
                <form method="POST" action="{{ route('admin.jadwal.destroy', $jadwal->id) }}"
                      onsubmit="return confirm('Hapus jadwal ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-full"
                            style="display:flex;align-items:center;justify-content:center;gap:8px;padding:10px;background:#fef2f2;color:#ef4444;border:1.5px solid #fecaca;border-radius:10px;font-size:0.875rem;font-weight:600;font-family:inherit;cursor:pointer;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                        </svg>
                        Hapus Jadwal Ini
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
.show-grid { display:grid; grid-template-columns:1fr 300px; gap:20px; align-items:start; }
.show-col-main { display:flex; flex-direction:column; gap:20px; }
.show-col-side  { display:flex; flex-direction:column; gap:20px; }
.detail-table { width:100%; border-collapse:collapse; }
.detail-table tr { border-bottom:1px solid var(--border); }
.detail-table tr:last-child { border-bottom:none; }
.detail-label { padding:12px 20px; font-size:0.8rem; font-weight:600; color:var(--text-muted); width:140px; white-space:nowrap; background:#fafafa; }
.detail-val { padding:12px 20px; font-size:0.875rem; color:var(--text-main); font-weight:500; }
@media(max-width:1024px) { .show-grid { grid-template-columns:1fr; } .show-col-side { order:-1; } }
</style>
@endpush

@push('scripts')
<script>
function toggleEdit() {
    const view = document.getElementById('viewMode');
    const edit = document.getElementById('editMode');
    const btn  = document.getElementById('btnEdit');
    const isEdit = edit.style.display !== 'none';

    view.style.display = isEdit ? '' : 'none';
    edit.style.display = isEdit ? 'none' : 'block';
    btn.textContent    = isEdit ? '✏️ Edit' : '✕ Batal Edit';
}

setTimeout(() => {
    const a = document.getElementById('flashAlert');
    if (a) a.style.opacity = '0', setTimeout(() => a.remove(), 400);
}, 4000);
</script>
@endpush