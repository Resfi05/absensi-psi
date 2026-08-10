@extends('layouts.admin')
@section('title', 'Manajemen Waktu')
@section('page-title', 'Manajemen Waktu')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-sep">/</span>
            <span class="breadcrumb-current">Manajemen Waktu</span>
        </nav>
        <h2 class="page-heading">Manajemen <span class="heading-accent">Waktu & Frekuensi</span></h2>
    </div>
    <button onclick="document.getElementById('modalTambah').classList.add('active')" class="btn-primary">
        + Tambah Frekuensi Baru
    </button>
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

<div class="card">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama (Kode)</th>
                    <th>Interval & Satuan</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($waktu as $i => $w)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <span style="font-weight:700;">{{ ucfirst(str_replace('_', ' ', $w->nama)) }}</span>
                        <div style="font-size:0.75rem;color:var(--text-muted);font-family:monospace;">{{ $w->nama }}</div>
                    </td>
                    <td>Setiap <b>{{ $w->interval }}</b> {{ ucfirst($w->satuan) }}</td>
                    <td>
                        <div class="action-btns" style="justify-content:center;">
                            {{-- Tombol Edit Berlogo --}}
                            <button type="button" onclick="openEdit({{ $w->id }}, '{{ $w->nama }}', {{ $w->interval }}, '{{ $w->satuan }}')" 
                                    class="btn-icon btn-icon-blue" title="Edit">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>
                            
                            {{-- Tombol Hapus Berlogo --}}
                            <form method="POST" action="{{ route('admin.waktu.destroy', $w->id) }}" onsubmit="return confirm('Hapus frekuensi ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon btn-icon-red" title="Hapus">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                                        <path d="M10 11v6M14 11v6"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 30px;">
                        <div style="color: var(--text-muted); font-size: 0.9rem;">
                            Belum ada data frekuensi. Tambahkan: harian, mingguan, bulanan.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal-overlay" id="modalTambah" onclick="this.classList.remove('active')">
    <div class="modal-box" style="max-width:400px" onclick="event.stopPropagation()">
        <div class="modal-header">
            <div class="modal-title">Tambah Frekuensi</div>
            <button class="modal-close" onclick="document.getElementById('modalTambah').classList.remove('active')">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.waktu.store') }}">
            @csrf
            <div class="modal-body" style="flex-direction:column;gap:14px;align-items:stretch;">
                <div class="form-group">
                    <label class="form-label required">Nama (Contoh: Tahunan, Kuartal)</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label required">Setiap (Angka)</label>
                        <input type="number" name="interval" class="form-control" min="1" value="1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Satuan</label>
                        <select name="satuan" class="form-control" required>
                            <option value="hari">Hari</option>
                            <option value="minggu">Minggu</option>
                            <option value="bulan">Bulan</option>
                            <option value="tahun">Tahun</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-primary" style="flex:1; justify-content:center;">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal-overlay" id="modalEdit" onclick="this.classList.remove('active')">
    <div class="modal-box" style="max-width:400px" onclick="event.stopPropagation()">
        <div class="modal-header">
            <div class="modal-title">Edit Frekuensi</div>
            <button class="modal-close" onclick="document.getElementById('modalEdit').classList.remove('active')">✕</button>
        </div>
        <form method="POST" id="editForm">
            @csrf @method('PUT')
            <div class="modal-body" style="flex-direction:column;gap:14px;align-items:stretch;">
                <div class="form-group">
                    <label class="form-label required">Nama</label>
                    <input type="text" name="nama" id="editNama" class="form-control" required>
                </div>
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label required">Setiap (Angka)</label>
                        <input type="number" name="interval" id="editInterval" class="form-control" min="1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Satuan</label>
                        <select name="satuan" id="editSatuan" class="form-control" required>
                            <option value="hari">Hari</option>
                            <option value="minggu">Minggu</option>
                            <option value="bulan">Bulan</option>
                            <option value="tahun">Tahun</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-primary" style="flex:1; justify-content:center;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEdit(id, nama, interval, satuan) {
    document.getElementById('editForm').action = '/admin/waktu/' + id;
    document.getElementById('editNama').value = nama;
    document.getElementById('editInterval').value = interval;
    document.getElementById('editSatuan').value = satuan;
    document.getElementById('modalEdit').classList.add('active');
}
setTimeout(() => {
    const a = document.getElementById('flashAlert');
    if (a) a.style.opacity = '0', setTimeout(() => a.remove(), 400);
}, 3000);
</script>
@endsection