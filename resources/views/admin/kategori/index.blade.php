@extends('layouts.admin')
@section('title', 'Kategori Barang')
@section('page-title', 'Kategori Barang')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-sep">/</span>
            <span class="breadcrumb-current">Kategori Barang</span>
        </nav>
        <h2 class="page-heading">Kelola <span class="heading-accent">Kategori Barang</span></h2>
    </div>
    <button onclick="document.getElementById('modalTambah').classList.add('active')" class="btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Tambah Kategori
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

<div class="kategori-grid">
    @forelse($kategori as $k)
    <div class="kategori-card">
        {{-- Header warna --}}
        <div class="kategori-card-top" style="background:{{ $k->warna }}15;border-bottom:3px solid {{ $k->warna }};">
            <div class="kategori-badge-wrap">
                <span class="kategori-badge" style="background:{{ $k->warna }};color:{{ $k->warnaText() }};">
                    {{ $k->kode }}
                </span>
                @if(!$k->is_active)
                    <span class="badge-status badge-gray" style="font-size:0.68rem;">Nonaktif</span>
                @endif
            </div>
            <div class="kategori-actions">
                <button onclick="openEdit({{ $k->id }}, '{{ $k->nama }}', '{{ $k->kode }}', '{{ $k->warna }}', `{{ $k->deskripsi }}`, `{{ $k->checklist }}`, {{ $k->is_active ? 1 : 0 }})"
                        class="btn-aksi btn-aksi-edit" title="Edit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </button>
                @if($k->barang_count === 0)
                <form method="POST" action="{{ route('admin.kategori.destroy', $k->id) }}"
                      onsubmit="return confirm('Hapus kategori {{ $k->nama }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-aksi btn-aksi-delete" title="Hapus">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                        </svg>
                    </button>
                </form>
                @endif
            </div>
        </div>

        <div class="kategori-card-body">
            <div class="kategori-nama">{{ $k->nama }}</div>
            <div class="kategori-desc">{{ $k->deskripsi ?? 'Tidak ada deskripsi' }}</div>
            <div class="kategori-stats">
                <div class="kategori-stat-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;">
                        <rect x="2" y="3" width="20" height="14" rx="2"/>
                        <line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
                    </svg>
                    <span>{{ $k->barang_count }} Barang</span>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1">
        <div class="empty-table-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="2" y="3" width="20" height="14" rx="2"/>
            </svg>
            <p>Belum ada kategori barang</p>
            <button onclick="document.getElementById('modalTambah').classList.add('active')" class="btn-primary btn-sm">
                + Tambah Kategori Pertama
            </button>
        </div>
    </div>
    @endforelse
</div>

{{-- Modal Tambah --}}
<div class="modal-overlay" id="modalTambah" onclick="this.classList.remove('active')">
    <div class="modal-box" style="max-width:500px" onclick="event.stopPropagation()">
        <div class="modal-header">
            <div>
                <div class="modal-title" style="font-family:inherit;">Tambah Kategori Baru</div>
                <div class="modal-sub">Buat kategori barang baru beserta checklist-nya</div>
            </div>
            <button class="modal-close" onclick="document.getElementById('modalTambah').classList.remove('active')">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.kategori.store') }}">
            @csrf
            <div class="modal-body" style="flex-direction:column;gap:14px;align-items:stretch;">
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label required">Nama Kategori</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: AC" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Kode</label>
                        <input type="text" name="kode" class="form-control" placeholder="AC"
                               oninput="this.value=this.value.toUpperCase()" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label required">Warna Badge</label>
                    <div style="display:flex;gap:10px;align-items:center;">
                        <input type="color" name="warna" value="#2563eb"
                               style="width:48px;height:40px;border:1.5px solid var(--border);border-radius:8px;cursor:pointer;padding:2px;">
                        <div id="warnaPreview" style="flex:1;padding:6px 12px;border-radius:8px;font-size:0.82rem;font-weight:700;background:#2563eb;color:white;text-align:center;">
                            Preview Badge
                        </div>
                    </div>
                </div>

                <div class="form-group" style="background:#f0fdf4; border:1px solid #bbf7d0; padding:12px; border-radius:8px;">
                    <label class="form-label" style="color:#166534; font-weight:700;">Daftar Checklist Pengecekan</label>
                    <textarea name="checklist" class="form-control form-textarea" rows="4" style="border-color:#bbf7d0;"
                              placeholder="Filter udara bersih&#10;Suhu normal&#10;Freon tidak bocor"></textarea>
                    <span class="form-hint" style="color:#15803d; margin-top:6px;">Pisahkan setiap item checklist dengan menekan <b>ENTER</b>. Ini akan otomatis muncul di form aplikasi teknisi.</span>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi Kategori</label>
                    <textarea name="deskripsi" class="form-control form-textarea" rows="2" placeholder="Deskripsi singkat..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-primary" style="flex:1;justify-content:center;">Simpan</button>
                <button type="button" class="btn-secondary" onclick="document.getElementById('modalTambah').classList.remove('active')">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal-overlay" id="modalEdit" onclick="this.classList.remove('active')">
    <div class="modal-box" style="max-width:500px" onclick="event.stopPropagation()">
        <div class="modal-header">
            <div>
                <div class="modal-title" style="font-family:inherit;">Edit Kategori</div>
                <div class="modal-sub">Ubah data kategori & checklist-nya</div>
            </div>
            <button class="modal-close" onclick="document.getElementById('modalEdit').classList.remove('active')">✕</button>
        </div>
        <form method="POST" id="editForm">
            @csrf @method('PUT')
            <div class="modal-body" style="flex-direction:column;gap:14px;align-items:stretch;">
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label required">Nama Kategori</label>
                        <input type="text" name="nama" id="editNama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Kode</label>
                        <input type="text" name="kode" id="editKode" class="form-control"
                               oninput="this.value=this.value.toUpperCase()" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label required">Warna Badge</label>
                    <div style="display:flex;gap:10px;align-items:center;">
                        <input type="color" name="warna" id="editWarna"
                               style="width:48px;height:40px;border:1.5px solid var(--border);border-radius:8px;cursor:pointer;padding:2px;"
                               oninput="updateEditPreview(this.value)">
                        <div id="editWarnaPreview" style="flex:1;padding:6px 12px;border-radius:8px;font-size:0.82rem;font-weight:700;text-align:center;">
                            Preview
                        </div>
                    </div>
                </div>

                <div class="form-group" style="background:#f0fdf4; border:1px solid #bbf7d0; padding:12px; border-radius:8px;">
                    <label class="form-label" style="color:#166534; font-weight:700;">Daftar Checklist Pengecekan</label>
                    <textarea name="checklist" id="editChecklist" class="form-control form-textarea" rows="4" style="border-color:#bbf7d0;"
                              placeholder="Filter udara bersih&#10;Suhu normal&#10;Freon tidak bocor"></textarea>
                    <span class="form-hint" style="color:#15803d; margin-top:6px;">Pisahkan setiap item checklist dengan menekan <b>ENTER</b>.</span>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" id="editDeskripsi" class="form-control form-textarea" rows="2"></textarea>
                </div>
                <div class="toggle-group">
                    <div class="toggle-info">
                        <div class="toggle-label">Status Aktif</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_active" id="editAktif" value="1">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-primary" style="flex:1;justify-content:center;">Simpan Perubahan</button>
                <button type="button" class="btn-secondary" onclick="document.getElementById('modalEdit').classList.remove('active')">Batal</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
.kategori-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 16px;
}
.kategori-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
    transition: transform 0.15s, box-shadow 0.15s;
}
.kategori-card:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,0.1); }
.kategori-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
}
.kategori-badge-wrap { display: flex; align-items: center; gap: 8px; }
.kategori-badge {
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 0.82rem;
    font-weight: 800;
    letter-spacing: 0.5px;
}
.kategori-actions { display: flex; gap: 6px; }
.kategori-card-body { padding: 16px; }
.kategori-nama { font-size: 1rem; font-weight: 700; color: var(--text-main); margin-bottom: 6px; }
.kategori-desc { font-size: 0.78rem; color: var(--text-muted); margin-bottom: 12px; line-height: 1.4; min-height: 36px; }
.kategori-stats { display: flex; gap: 12px; }
.kategori-stat-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--text-muted);
}
</style>
@endpush

@push('scripts')
<script>
document.querySelector('[name="warna"]')?.addEventListener('input', function() {
    const prev = document.getElementById('warnaPreview');
    prev.style.background = this.value;
    prev.style.color = getLuminance(this.value) > 0.5 ? '#1e293b' : '#ffffff';
});

function updateEditPreview(val) {
    const prev = document.getElementById('editWarnaPreview');
    prev.style.background = val;
    prev.style.color = getLuminance(val) > 0.5 ? '#1e293b' : '#ffffff';
}

function getLuminance(hex) {
    hex = hex.replace('#','');
    const r = parseInt(hex.substr(0,2),16)/255;
    const g = parseInt(hex.substr(2,2),16)/255;
    const b = parseInt(hex.substr(4,2),16)/255;
    return 0.299*r + 0.587*g + 0.114*b;
}

function openEdit(id, nama, kode, warna, deskripsi, checklist, aktif) {
    document.getElementById('editForm').action = '/admin/kategori/' + id;
    document.getElementById('editNama').value      = nama;
    document.getElementById('editKode').value      = kode;
    document.getElementById('editWarna').value     = warna;
    document.getElementById('editDeskripsi').value = deskripsi && deskripsi !== 'null' ? deskripsi : '';
    document.getElementById('editChecklist').value = checklist && checklist !== 'null' ? checklist : '';
    document.getElementById('editAktif').checked   = aktif == 1;
    updateEditPreview(warna);
    document.getElementById('modalEdit').classList.add('active');
}

setTimeout(() => {
    const a = document.getElementById('flashAlert');
    if (a) a.style.opacity = '0', setTimeout(() => a.remove(), 400);
}, 4000);
</script>
@endpush