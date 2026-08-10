@extends('layouts.admin')
@section('title', 'Lokasi Barang')
@section('page-title', 'Lokasi Barang')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-current">Lokasi Barang</span>
        </nav>
        <h2 class="page-heading">Master <span class="heading-accent">Lokasi Barang</span></h2>
    </div>
    <button onclick="document.getElementById('modalTambah').classList.add('active')" class="btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Tambah Lokasi
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
@if(session('error'))
<div class="alert alert-error" id="flashAlert">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    {{ session('error') }}
    <button class="alert-close" onclick="this.parentElement.remove()">✕</button>
</div>
@endif

{{-- Hero Info Band --}}
<div class="lokasi-hero-band">
    <div class="lokasi-hero-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
            <circle cx="12" cy="10" r="3"/>
        </svg>
    </div>
    <div class="lokasi-hero-text">
        <div class="lokasi-hero-title">Satu nama, satu tempat — selalu</div>
        <div class="lokasi-hero-sub">
            Gunakan format konsisten <code>Gedung — Lantai — Ruangan</code> agar setiap barang dan jadwal mengacu ke lokasi yang sama persis, tanpa duplikasi atau typo.
        </div>
    </div>
</div>

{{-- Stats Row --}}
<div class="lokasi-stats-row">
    <div class="lokasi-stat-card">
        <div class="lokasi-stat-icon" style="background:#dbeafe;color:#2563eb;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                <circle cx="12" cy="10" r="3"/>
            </svg>
        </div>
        <div>
            <div class="lokasi-stat-val">{{ $lokasi->count() }}</div>
            <div class="lokasi-stat-label">Total Lokasi</div>
        </div>
    </div>
    <div class="lokasi-stat-card">
        <div class="lokasi-stat-icon" style="background:#dcfce7;color:#16a34a;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>
        <div>
            <div class="lokasi-stat-val">{{ $lokasi->where('is_active',true)->count() }}</div>
            <div class="lokasi-stat-label">Lokasi Aktif</div>
        </div>
    </div>
    <div class="lokasi-stat-card">
        <div class="lokasi-stat-icon" style="background:#fef3c7;color:#d97706;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="2" y="3" width="20" height="14" rx="2"/>
                <line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
            </svg>
        </div>
        <div>
            <div class="lokasi-stat-val">{{ $lokasi->sum('barang_count') }}</div>
            <div class="lokasi-stat-label">Total Barang Terdaftar</div>
        </div>
    </div>
    <div class="lokasi-stat-card">
        <div class="lokasi-stat-icon" style="background:#fee2e2;color:#ef4444;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>
        <div>
            <div class="lokasi-stat-val">{{ $lokasi->where('barang_count',0)->count() }}</div>
            <div class="lokasi-stat-label">Belum Ada Barang</div>
        </div>
    </div>
</div>

{{-- Lokasi Grid Cards --}}
<div class="lokasi-grid">
    @forelse($lokasi as $lok)
    <a href="{{ route('admin.lokasi.show', $lok->id) }}" class="lokasi-card {{ !$lok->is_active ? 'lokasi-card-inactive' : '' }}">
        <div class="lokasi-card-top">
            <div class="lokasi-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
            </div>
            <div class="lokasi-card-top-right">
                <span class="badge-status {{ $lok->is_active ? 'badge-green' : 'badge-red' }} lokasi-status-badge" style="font-size:0.68rem;">
                    {{ $lok->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
                <div class="lokasi-card-actions">
                    <button onclick="event.stopPropagation();event.preventDefault();openEdit({{ $lok->id }}, '{{ addslashes($lok->nama) }}', '{{ $lok->kode }}', '{{ addslashes($lok->deskripsi) }}', {{ $lok->is_active ? 1 : 0 }})"
                            class="lokasi-mini-btn" title="Edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </button>
                    @if($lok->barang_count === 0)
                    <button onclick="event.stopPropagation();event.preventDefault();confirmDeleteLokasi({{ $lok->id }}, '{{ addslashes($lok->nama) }}')"
                            class="lokasi-mini-btn lokasi-mini-btn-danger" title="Hapus">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                        </svg>
                    </button>
                    @else
                    <span class="lokasi-mini-btn lokasi-mini-btn-disabled" title="Tidak bisa dihapus — masih ada barang" onclick="event.stopPropagation();event.preventDefault();">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                        </svg>
                    </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="lokasi-card-body">
            <div class="lokasi-card-name">{{ $lok->nama }}</div>
            @if($lok->kode)
                <span class="lokasi-card-kode">{{ $lok->kode }}</span>
            @endif
            <div class="lokasi-card-desc">{{ $lok->deskripsi ?? 'Belum ada deskripsi' }}</div>
        </div>

        <div class="lokasi-card-footer">
            <div class="lokasi-card-count">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;">
                    <rect x="2" y="3" width="20" height="14" rx="2"/>
                    <line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
                </svg>
                <strong>{{ $lok->barang_count }}</strong> barang
            </div>
            <span class="lokasi-card-arrow">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                </svg>
            </span>
        </div>
    </a>
    @empty
    <div style="grid-column:1/-1;">
        <div class="empty-table-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                <circle cx="12" cy="10" r="3"/>
            </svg>
            <p>Belum ada lokasi — mulai tambah master lokasi</p>
            <button onclick="document.getElementById('modalTambah').classList.add('active')" class="btn-primary btn-sm">
                + Tambah Lokasi Pertama
            </button>
        </div>
    </div>
    @endforelse
</div>

{{-- Hidden delete forms --}}
<div id="deleteFormsContainer" style="display:none;">
    @foreach($lokasi as $lok)
        @if($lok->barang_count === 0)
        <form method="POST" action="{{ route('admin.lokasi.destroy', $lok->id) }}" id="deleteForm{{ $lok->id }}">
            @csrf @method('DELETE')
        </form>
        @endif
    @endforeach
</div>

{{-- Modal Tambah --}}
<div class="modal-overlay" id="modalTambah" onclick="this.classList.remove('active')">
    <div class="modal-box" style="max-width:480px" onclick="event.stopPropagation()">
        <div class="modal-header">
            <div>
                <div class="modal-title" style="font-family:inherit;">Tambah Lokasi Baru</div>
                <div class="modal-sub">Buat master data lokasi barang</div>
            </div>
            <button class="modal-close" onclick="document.getElementById('modalTambah').classList.remove('active')">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.lokasi.store') }}">
            @csrf
            <div class="modal-body" style="flex-direction:column;gap:14px;align-items:stretch;">
                <div class="form-group">
                    <label class="form-label required">Nama Lokasi</label>
                    <input type="text" name="nama" class="form-control"
                           placeholder="Gedung A - Lantai 1 - Ruang Meeting" required>
                    <span class="form-hint">Gunakan format: Gedung - Lantai - Ruangan (spesifik dan konsisten)</span>
                </div>
                <div class="form-group">
                    <label class="form-label">Kode Singkat <span style="font-size:0.72rem;color:var(--text-muted);font-weight:400;">(opsional)</span></label>
                    <input type="text" name="kode" class="form-control"
                           placeholder="GDA-L1-RM" oninput="this.value=this.value.toUpperCase()">
                    <span class="form-hint">Kode singkat untuk referensi cepat</span>
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control form-textarea" rows="2"
                              placeholder="Keterangan tambahan lokasi..."></textarea>
                </div>
                <div style="background:#fef3c7;border-radius:8px;padding:10px 12px;font-size:0.78rem;color:#d97706;">
                    ⚠️ Pastikan nama lokasi sudah benar sebelum disimpan. Nama ini akan dipakai sebagai acuan oleh semua barang yang ada di lokasi ini.
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-primary" style="flex:1;justify-content:center;">Simpan Lokasi</button>
                <button type="button" class="btn-secondary"
                        onclick="document.getElementById('modalTambah').classList.remove('active')">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal-overlay" id="modalEdit" onclick="this.classList.remove('active')">
    <div class="modal-box" style="max-width:480px" onclick="event.stopPropagation()">
        <div class="modal-header">
            <div>
                <div class="modal-title" style="font-family:inherit;">Edit Lokasi</div>
                <div class="modal-sub">Perbarui data master lokasi</div>
            </div>
            <button class="modal-close" onclick="document.getElementById('modalEdit').classList.remove('active')">✕</button>
        </div>
        <form method="POST" id="editForm">
            @csrf @method('PUT')
            <div class="modal-body" style="flex-direction:column;gap:14px;align-items:stretch;">
                <div class="form-group">
                    <label class="form-label required">Nama Lokasi</label>
                    <input type="text" name="nama" id="editNama" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kode Singkat</label>
                    <input type="text" name="kode" id="editKode" class="form-control"
                           oninput="this.value=this.value.toUpperCase()">
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" id="editDeskripsi" class="form-control form-textarea" rows="2"></textarea>
                </div>
                <div class="toggle-group">
                    <div class="toggle-info">
                        <div class="toggle-label">Status Aktif</div>
                        <div class="toggle-desc">Lokasi nonaktif tidak bisa dipilih untuk barang baru</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_active" id="editAktif" value="1">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div style="background:#fee2e2;border-radius:8px;padding:10px 12px;font-size:0.78rem;color:#ef4444;">
                    ⚠️ Hati-hati mengubah nama lokasi — semua barang yang terhubung akan otomatis ikut berubah.
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-primary" style="flex:1;justify-content:center;">Simpan Perubahan</button>
                <button type="button" class="btn-secondary"
                        onclick="document.getElementById('modalEdit').classList.remove('active')">Batal</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
.lokasi-hero-band {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #3b82f6 100%);
    border-radius: 16px;
    padding: 22px 26px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 18px;
    position: relative;
    overflow: hidden;
}
.lokasi-hero-band::after {
    content: '';
    position: absolute;
    right: -40px; top: -40px;
    width: 160px; height: 160px;
    background: rgba(255,255,255,0.06);
    border-radius: 50%;
}
.lokasi-hero-icon {
    width: 50px; height: 50px;
    background: rgba(255,255,255,0.15);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    backdrop-filter: blur(4px);
}
.lokasi-hero-icon svg { width: 24px; height: 24px; }
.lokasi-hero-title { font-size: 1rem; font-weight: 800; color: white; margin-bottom: 4px; }
.lokasi-hero-sub { font-size: 0.8rem; color: rgba(255,255,255,0.85); line-height: 1.5; max-width: 640px; }
.lokasi-hero-sub code { background: rgba(255,255,255,0.18); padding: 2px 6px; border-radius: 5px; font-size: 0.76rem; }

.lokasi-stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
    gap: 14px;
    margin-bottom: 24px;
}
.lokasi-stat-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 16px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: var(--shadow);
}
.lokasi-stat-icon {
    width: 42px; height: 42px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.lokasi-stat-icon svg { width: 20px; height: 20px; }
.lokasi-stat-val { font-size: 1.4rem; font-weight: 800; color: var(--text-main); line-height: 1.1; }
.lokasi-stat-label { font-size: 0.74rem; color: var(--text-muted); margin-top: 2px; }

.lokasi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
    gap: 18px;
}
.lokasi-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 14px;
    box-shadow: var(--shadow);
    padding: 18px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    text-decoration: none;
    color: inherit;
    position: relative;
    transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
}
.lokasi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 24px rgba(37,99,235,0.14);
    border-color: #bfdbfe;
}
.lokasi-card-inactive { opacity: 0.65; }
.lokasi-card-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; }
.lokasi-card-top-right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
.lokasi-status-badge { white-space: nowrap; transition: opacity 0.15s, transform 0.15s; }
.lokasi-card-icon {
    width: 42px; height: 42px;
    background: linear-gradient(135deg,#eff6ff,#dbeafe);
    color: #2563eb;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.lokasi-card-icon svg { width: 20px; height: 20px; }
.lokasi-card-body { flex: 1; }
.lokasi-card-name {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--text-main);
    line-height: 1.35;
    margin-bottom: 6px;
}
.lokasi-card-kode {
    display: inline-block;
    font-family: monospace;
    font-size: 0.7rem;
    font-weight: 700;
    color: #2563eb;
    background: #eff6ff;
    padding: 2px 8px;
    border-radius: 5px;
    margin-bottom: 8px;
}
.lokasi-card-desc {
    font-size: 0.78rem;
    color: var(--text-muted);
    line-height: 1.45;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
.lokasi-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 12px;
    border-top: 1px solid var(--border);
}
.lokasi-card-count {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.78rem;
    color: var(--text-muted);
}
.lokasi-card-count strong { color: var(--text-main); font-size: 0.85rem; }
.lokasi-card-arrow {
    width: 28px; height: 28px;
    background: #f1f5f9;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #94a3b8;
    transition: background 0.18s, color 0.18s, transform 0.18s;
}
.lokasi-card-arrow svg { width: 14px; height: 14px; }
.lokasi-card:hover .lokasi-card-arrow {
    background: #2563eb; color: white; transform: translateX(2px);
}
.lokasi-card-actions {
    display: flex; gap: 6px;
    max-width: 0;
    overflow: hidden;
    opacity: 0;
    transition: max-width 0.22s ease, opacity 0.18s ease, gap 0.22s ease;
}
.lokasi-card:hover .lokasi-card-actions {
    max-width: 80px;
    opacity: 1;
}
.lokasi-mini-btn {
    width: 28px; height: 28px;
    border-radius: 8px;
    background: white;
    border: 1px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    color: #64748b;
    cursor: pointer;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    flex-shrink: 0;
}
.lokasi-mini-btn svg { width: 13px; height: 13px; }
.lokasi-mini-btn:hover { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
.lokasi-mini-btn-danger:hover { background: #fef2f2; color: #ef4444; border-color: #fecaca; }
.lokasi-mini-btn-disabled { cursor: not-allowed; color: #cbd5e1; }
.lokasi-mini-btn-disabled:hover { background: white; color: #cbd5e1; }
</style>
@endpush

@push('scripts')
<script>
function openEdit(id, nama, kode, deskripsi, aktif) {
    document.getElementById('editForm').action = '/admin/lokasi/' + id;
    document.getElementById('editNama').value      = nama;
    document.getElementById('editKode').value      = kode || '';
    document.getElementById('editDeskripsi').value = deskripsi || '';
    document.getElementById('editAktif').checked   = aktif == 1;
    document.getElementById('modalEdit').classList.add('active');
}

function confirmDeleteLokasi(id, nama) {
    if (confirm('Hapus lokasi "' + nama + '"?')) {
        document.getElementById('deleteForm' + id).submit();
    }
}

setTimeout(() => {
    const a = document.getElementById('flashAlert');
    if (a) a.style.opacity = '0', setTimeout(() => a.remove(), 400);
}, 4000);
</script>
@endpush