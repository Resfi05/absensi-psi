@extends('layouts.admin')
@section('title', 'Buat Jadwal')
@section('page-title', 'Buat Jadwal')

@push('styles')
<style>
    /* CSS Khusus untuk Panel Pemilihan Massal */
    .bulk-select-panel {
        border: 1px solid var(--border);
        border-radius: 10px;
        background: #fff;
        overflow: hidden;
        margin-bottom: 20px;
    }
    .bulk-filter-bar {
        background: #f8fafc;
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
        display: grid;
        grid-template-columns: 1fr 1fr 1.5fr;
        gap: 10px;
    }
    .bulk-filter-bar select, .bulk-filter-bar input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 0.8rem;
        outline: none;
    }
    .bulk-filter-bar input:focus, .bulk-filter-bar select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .bulk-list-container {
        max-height: 280px;
        overflow-y: auto;
        padding: 8px;
    }
    .item-row {
        display: flex;
        align-items: center;
        padding: 10px 12px;
        border-radius: 8px;
        margin-bottom: 4px;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    .item-row:hover {
        background: #f1f5f9;
    }
    .item-row.selected {
        background: #eff6ff;
        border-color: #bfdbfe;
    }
    .item-checkbox {
        width: 18px;
        height: 18px;
        margin-right: 14px;
        cursor: pointer;
        pointer-events: none; /* Biar klik row-nya yang jalan */
    }
    .item-info {
        flex: 1;
        min-width: 0;
    }
    .item-name {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 2px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .item-kode {
        font-family: monospace;
        font-size: 0.72rem;
        background: #e2e8f0;
        padding: 2px 6px;
        border-radius: 4px;
        color: #475569;
    }
    .item-desc {
        font-size: 0.75rem;
        color: #64748b;
        display: flex;
        gap: 12px;
        align-items: center;
    }
    .badge-kategori-mini {
        font-size: 0.7rem;
        padding: 1px 6px;
        border-radius: 4px;
        font-weight: 600;
    }
    .selection-summary {
        padding: 12px 16px;
        background: #f8fafc;
        border-top: 1px solid var(--border);
        font-size: 0.85rem;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-sep">/</span>
            <a href="{{ route('admin.jadwal.index') }}" class="breadcrumb-link">Jadwal Pengecekan</a>
            <span class="breadcrumb-sep">/</span>
            <span class="breadcrumb-current">Buat Jadwal Massal</span>
        </nav>
        <h2 class="page-heading">Buat <span class="heading-accent">Jadwal Massal</span></h2>
    </div>
    <a href="{{ route('admin.jadwal.index') }}" class="btn-secondary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
        </svg>
        Kembali
    </a>
</div>

@if(session('error'))
<div class="alert alert-error" id="flashAlert">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    {{ session('error') }}
    <button class="alert-close" onclick="this.parentElement.remove()">✕</button>
</div>
@endif

@if($barang_belum_siap > 0)
<div style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:14px 18px;margin-bottom:20px;display:flex;gap:12px;align-items:flex-start;">
    <svg viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" style="width:20px;height:20px;flex-shrink:0;margin-top:1px;">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    <div>
        <div style="font-size:0.85rem;font-weight:700;color:#92400e;">{{ $barang_belum_siap }} barang belum bisa dijadwalkan</div>
        <div style="font-size:0.8rem;color:#92400e;margin-top:2px;">
            Barang yang belum memiliki Lokasi atau Kategori tidak akan muncul di daftar pilihan di bawah. Lengkapi datanya dulu di
            <a href="{{ route('admin.barang.index') }}" style="font-weight:700;text-decoration:underline;">Kelola Data Barang</a>.
        </div>
    </div>
</div>
@endif

<div class="form-grid">
    <div class="form-col-main">
        <form method="POST" action="{{ route('admin.jadwal.store') }}" id="formJadwal">
            @csrf
            
            {{-- 📦 PANEL PEMILIHAN BARANG MASSAL 📦 --}}
            <div class="form-card" style="margin-bottom: 24px;">
                <div class="form-card-header">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                        <line x1="12" y1="22.08" x2="12" y2="12"/>
                    </svg>
                    Pilih Barang (Bisa lebih dari 1)
                </div>
                
                <div class="form-card-body" style="padding: 16px;">
                    <div class="bulk-select-panel">
                        <div class="bulk-filter-bar">
                            <select id="filterKat" onchange="filterBarang()">
                                <option value="all">Semua Kategori</option>
                                @foreach($kategori_list as $k)
                                    <option value="{{ $k->nama }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                            
                            <select id="filterLok" onchange="filterBarang()">
                                <option value="all">Semua Lokasi</option>
                                @php $lokasi_unik = $barang_list->pluck('lokasiRelasi.nama')->unique()->filter(); @endphp
                                @foreach($lokasi_unik as $lok)
                                    <option value="{{ $lok }}">{{ $lok }}</option>
                                @endforeach
                            </select>

                            <input type="text" id="filterSearch" placeholder="🔍 Cari nama atau kode..." onkeyup="filterBarang()">
                        </div>

                        <div class="bulk-list-container" id="barangListContainer">
                            @forelse($barang_list as $b)
                            <div class="item-row" 
                                 onclick="toggleBarang(this)"
                                 data-kat="{{ $b->kategori->nama }}" 
                                 data-lok="{{ $b->lokasiRelasi->nama ?? '' }}" 
                                 data-text="{{ strtolower($b->kode_barang . ' ' . $b->nama_barang) }}">
                                
                                <input type="checkbox" name="barang_id[]" value="{{ $b->id }}" class="item-checkbox" {{ in_array($b->id, old('barang_id', [])) ? 'checked' : '' }}>
                                
                                <div class="item-info">
                                    <div class="item-name">
                                        {{ $b->nama_barang }}
                                        <span class="item-kode">{{ $b->kode_barang }}</span>
                                    </div>
                                    <div class="item-desc">
                                        <span class="badge-kategori-mini" style="background:{{ $b->kategori->warna }}20; color:{{ $b->kategori->warna }}; border:1px solid {{ $b->kategori->warna }}40;">
                                            {{ $b->kategori->nama }}
                                        </span>
                                        <span>📍 {{ $b->lokasiRelasi->nama ?? '???' }}</span>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div style="padding: 20px; text-align:center; color:#94a3b8; font-size:0.85rem;">
                                Tidak ada data barang yang siap dijadwalkan.
                            </div>
                            @endforelse
                        </div>

                        <div class="selection-summary">
                            <span id="txtTerpilih" style="color: #475569;">0 barang terpilih</span>
                            <button type="button" onclick="pilihSemuaTampil()" style="background:none; border:none; color:#2563eb; font-size:0.8rem; font-weight:700; cursor:pointer; text-decoration:underline;">
                                Pilih Semua (di layar)
                            </button>
                        </div>
                    </div>
                    @error('barang_id')<span class="form-error" style="margin-top:-10px;display:block;">⚠️ Wajib memilih minimal 1 barang.</span>@enderror
                </div>
            </div>

            {{-- ⚙️ PANEL PENGATURAN JADWAL & PETUGAS ⚙️ --}}
            <div class="form-card">
                <div class="form-card-header">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Pengaturan Jadwal & Penugasan
                </div>
                <div class="form-card-body">

                    {{-- 🧑‍🔧 PANEL PEMILIHAN PETUGAS 🧑‍🔧 --}}
                    <div class="form-group">
                        <label class="form-label required">Tugaskan Kepada (Petugas)</label>
                        <div class="bulk-select-panel" style="margin-bottom: 0;">
                            <div class="bulk-filter-bar" style="grid-template-columns: 1fr 1.5fr; padding: 10px 16px;">
                                <select id="filterUserSpesialis" onchange="filterUser()">
                                    <option value="all">Semua Spesialis</option>
                                    <option value="umum">Bisa Semua (Umum)</option>
                                    @foreach($kategori_list as $k)
                                        <option value="{{ strtolower($k->nama) }}">{{ $k->nama }}</option>
                                    @endforeach
                                </select>
                                <input type="text" id="filterUserSearch" placeholder="🔍 Cari nama petugas..." onkeyup="filterUser()">
                            </div>
                            
                            <div class="bulk-list-container" id="userListContainer" style="max-height: 220px;">
                                @foreach($user_list as $u)
                                    @php 
                                        // 🔥 REVISI: Logika Spesialisasi Banyak (Many-to-Many)
                                        $hasSpesialisasi = $u->spesialisasi && $u->spesialisasi->count() > 0;
                                        
                                        // Buat string berisi daftar spesialisasi untuk JS filter (misal: "ac,cctv")
                                        $arrSpesialis = $hasSpesialisasi 
                                            ? $u->spesialisasi->pluck('nama')->map(fn($n) => strtolower($n))->toArray() 
                                            : ['umum'];
                                        $strSpesialisData = implode(',', $arrSpesialis);

                                        // Warna untuk avatar (ambil warna dari spesialisasi pertama, atau default)
                                        $warna = $hasSpesialisasi ? $u->spesialisasi->first()->warna : '#64748b';
                                    @endphp
                                    <div class="item-row user-item-row" 
                                         onclick="toggleUser(this)"
                                         data-spesialis="{{ $strSpesialisData }}"
                                         data-text="{{ strtolower($u->name) }}">
                                        
                                        <input type="checkbox" name="user_id[]" value="{{ $u->id }}" class="item-checkbox user-checkbox" {{ in_array($u->id, old('user_id', [])) ? 'checked' : '' }}>
                                        
                                        {{-- Avatar Mini Petugas --}}
                                        @if($u->foto)
                                            <img src="{{ Storage::url($u->foto) }}" alt="{{ $u->name }}" 
                                                 style="width:34px; height:34px; min-width:34px; border-radius:50%; object-fit:cover; margin-right:12px; flex-shrink:0; border:1px solid var(--border);">
                                        @else
                                            <div style="width:34px; height:34px; min-width:34px; border-radius:50%; background:{{ $warna }}15; color:{{ $warna }}; display:flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:800; margin-right:12px; flex-shrink:0; border: 1px solid {{ $warna }}40;">
                                                {{ strtoupper(substr($u->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        
                                        <div class="item-info">
                                            <div class="item-name" style="margin-bottom: 2px;">{{ $u->name }}</div>
                                            <div class="item-desc">
                                                @if($hasSpesialisasi)
                                                    <div style="display:flex; flex-wrap:wrap; gap:4px;">
                                                        @foreach($u->spesialisasi as $sp)
                                                            <span class="badge-kategori-mini" style="background:{{ $sp->warna }}15; color:{{ $sp->warna }}; border:1px solid {{ $sp->warna }}40;">
                                                                {{ $sp->nama }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="badge-kategori-mini" style="background:#f8fafc; color:#64748b; border:1px solid #cbd5e1;">
                                                        Bisa Semua (Umum)
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="selection-summary" style="padding: 10px 16px; background:#f8fafc; border-top:1px solid var(--border);">
                                <span id="txtTerpilihUser" style="color: #475569;">0 petugas terpilih</span>
                                <button type="button" onclick="pilihSemuaUserTampil()" style="background:none; border:none; color:#2563eb; font-size:0.8rem; font-weight:700; cursor:pointer; text-decoration:underline;">
                                    Pilih Semua (di layar)
                                </button>
                            </div>
                        </div>
                        @error('user_id')<span class="form-error" style="display:block; margin-top:6px;">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label required">Tanggal Mulai Pengecekan</label>
                            <input type="date" name="tanggal_mulai"
                                   class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                   value="{{ old('tanggal_mulai', now()->format('Y-m-d')) }}"
                                   onchange="updatePreviewNext()">
                            @error('tanggal_mulai')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Frekuensi Pengecekan</label>
                            <select name="frekuensi" id="frekuensiSelect" class="form-control @error('frekuensi') is-invalid @enderror"
                                    onchange="updatePreviewNext()">
                                @foreach($waktu_list as $w)
                                    <option value="{{ $w->nama }}" 
                                            data-interval="{{ $w->interval }}" 
                                            data-satuan="{{ $w->satuan }}"
                                            {{ old('frekuensi') == $w->nama ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $w->nama)) }} (Tiap {{ $w->interval }} {{ ucfirst($w->satuan) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('frekuensi')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Estimasi Jam Pengecekan <span style="font-size:0.72rem;color:var(--text-muted);font-weight:400;">(opsional)</span>
                        </label>
                        <div class="form-row-2" style="gap:12px;">
                            <div>
                                <input type="time" name="jam_mulai" class="form-control @error('jam_mulai') is-invalid @enderror"
                                       value="{{ old('jam_mulai') }}" placeholder="Jam mulai">
                                @error('jam_mulai')<span class="form-error">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <input type="time" name="jam_selesai" class="form-control @error('jam_selesai') is-invalid @enderror"
                                       value="{{ old('jam_selesai') }}" placeholder="Jam selesai">
                                @error('jam_selesai')<span class="form-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div id="nextPreview" style="background:#eff6ff;border-radius:8px;padding:12px 16px;border:1px solid #bfdbfe; margin-bottom: 15px;">
                        <div style="font-size:0.78rem;font-weight:600;color:#2563eb;margin-bottom:4px;">
                            📅 Jadwal Berikutnya (otomatis)
                        </div>
                        <div id="nextDate" style="font-size:0.875rem;font-weight:700;color:var(--text-main);">...</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Keterangan Tambahan</label>
                        <textarea name="keterangan" class="form-control form-textarea" rows="3"
                                  placeholder="Berlaku untuk semua barang yang dipilih (opsional)...">{{ old('keterangan') }}</textarea>
                    </div>

                    <div style="display:flex;gap:10px;padding-top:4px;">
                        <button type="submit" class="btn-primary" onclick="return validasiSubmit()" {{ $barang_list->isEmpty() ? 'disabled' : '' }}>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v14a2 2 0 01-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                            </svg>
                            Simpan & Tugaskan Massal
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
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                Info Buat Jadwal Massal
            </div>
            <div class="form-card-body" style="gap:12px;">
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <div style="width:24px;height:24px;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:0.72rem;font-weight:700;color:#2563eb;">1</div>
                    <div style="font-size:0.8rem;color:var(--text-muted);line-height:1.5;">Gunakan filter kategori dan pencarian untuk menemukan barang dengan cepat. Anda bebas memilih berapapun jumlah barang.</div>
                </div>
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <div style="width:24px;height:24px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:0.72rem;font-weight:700;color:#16a34a;">2</div>
                    <div style="font-size:0.8rem;color:var(--text-muted);line-height:1.5;">Pengaturan jadwal (Tanggal, Frekuensi, Jam, dan Petugas) akan diterapkan <b>sama persis</b> ke semua barang yang Anda centang.</div>
                </div>
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <div style="width:24px;height:24px;background:#fef3c7;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:0.72rem;font-weight:700;color:#d97706;">3</div>
                    <div style="font-size:0.8rem;color:var(--text-muted);line-height:1.5;">Sistem akan otomatis meng-<i>generate</i> jadwal rutin ke depan (sampai akhir tahun) untuk masing-masing barang.</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// --- LOGIKA FILTER & SELECT BARANG ---
function filterBarang() {
    const kat = document.getElementById('filterKat').value;
    const lok = document.getElementById('filterLok').value;
    const search = document.getElementById('filterSearch').value.toLowerCase();
    
    const rows = document.querySelectorAll('.item-row:not(.user-item-row)');
    
    rows.forEach(row => {
        const rowKat = row.getAttribute('data-kat');
        const rowLok = row.getAttribute('data-lok');
        const rowText = row.getAttribute('data-text');
        
        let match = true;
        if (kat !== 'all' && rowKat !== kat) match = false;
        if (lok !== 'all' && rowLok !== lok) match = false;
        if (search && !rowText.includes(search)) match = false;
        
        row.style.display = match ? 'flex' : 'none';
    });
}

function toggleBarang(rowElement) {
    const checkbox = rowElement.querySelector('.item-checkbox');
    checkbox.checked = !checkbox.checked;
    
    if (checkbox.checked) {
        rowElement.classList.add('selected');
    } else {
        rowElement.classList.remove('selected');
    }
    hitungTerpilih();
}

function pilihSemuaTampil() {
    const rows = document.querySelectorAll('.item-row:not(.user-item-row)');
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            const cb = row.querySelector('.item-checkbox');
            cb.checked = true;
            row.classList.add('selected');
        }
    });
    hitungTerpilih();
}

function hitungTerpilih() {
    const count = document.querySelectorAll('.item-checkbox:not(.user-checkbox):checked').length;
    const txtEl = document.getElementById('txtTerpilih');
    txtEl.textContent = count + ' barang terpilih';
    
    if (count > 0) {
        txtEl.style.color = '#2563eb';
        txtEl.style.fontWeight = '700';
    } else {
        txtEl.style.color = '#475569';
        txtEl.style.fontWeight = '600';
    }
}

// --- LOGIKA FILTER & SELECT PETUGAS (REVISI MULTI-SPESIALISASI) ---
function filterUser() {
    const search = document.getElementById('filterUserSearch').value.toLowerCase();
    const spesialisSelect = document.getElementById('filterUserSpesialis').value; // 'all', 'umum', 'ac', dll
    const rows = document.querySelectorAll('.user-item-row');
    
    rows.forEach(row => {
        const rowText = row.getAttribute('data-text');
        const rowSpesialisStr = row.getAttribute('data-spesialis'); // contoh "ac,cctv"
        const arrSpesialis = rowSpesialisStr.split(','); // jadikan array ['ac', 'cctv']
        
        let match = true;
        if (search && !rowText.includes(search)) match = false;
        
        // Logika mencocokkan spesialisasi (apakah pilihan filter ada di dalam array spesialisasi user)
        if (spesialisSelect !== 'all') {
            if (!arrSpesialis.includes(spesialisSelect)) {
                match = false;
            }
        }
        
        row.style.display = match ? 'flex' : 'none';
    });
}

function pilihSemuaUserTampil() {
    const rows = document.querySelectorAll('.user-item-row');
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            const cb = row.querySelector('.user-checkbox');
            cb.checked = true;
            row.classList.add('selected');
        }
    });
    hitungTerpilihUser();
}

function toggleUser(rowElement) {
    const checkbox = rowElement.querySelector('.user-checkbox');
    checkbox.checked = !checkbox.checked;
    
    if (checkbox.checked) {
        rowElement.classList.add('selected');
    } else {
        rowElement.classList.remove('selected');
    }
    
    hitungTerpilihUser();
}

function hitungTerpilihUser() {
    const count = document.querySelectorAll('.user-checkbox:checked').length;
    const txtEl = document.getElementById('txtTerpilihUser');
    txtEl.textContent = count + ' petugas terpilih';
    
    if (count > 0) {
        txtEl.style.color = '#2563eb';
        txtEl.style.fontWeight = '700';
    } else {
        txtEl.style.color = '#475569';
        txtEl.style.fontWeight = '600';
    }
}

// --- VALIDASI SUBMIT ---
function validasiSubmit() {
    const countBarang = document.querySelectorAll('.item-checkbox:not(.user-checkbox):checked').length;
    const countUser = document.querySelectorAll('.user-checkbox:checked').length;
    
    if (countBarang === 0) {
        alert('Mohon pilih minimal 1 barang dari daftar.');
        return false;
    }
    if (countUser === 0) {
        alert('Mohon pilih minimal 1 petugas untuk mengerjakan jadwal ini.');
        return false;
    }
    return true;
}

// --- LOGIKA TANGGAL SELANJUTNYA ---
function updatePreviewNext() {
    const tgl = document.querySelector('[name="tanggal_mulai"]').value;
    const sel = document.getElementById('frekuensiSelect');
    const opt = sel.options[sel.selectedIndex];

    if (!tgl || !opt) return;

    const interval = parseInt(opt.dataset.interval) || 1;
    const satuan = opt.dataset.satuan;
    const date = new Date(tgl);

    if (satuan === 'hari')   date.setDate(date.getDate() + interval);
    if (satuan === 'minggu') date.setDate(date.getDate() + (interval * 7));
    if (satuan === 'bulan')  date.setMonth(date.getMonth() + interval);
    if (satuan === 'tahun')  date.setFullYear(date.getFullYear() + interval);

    const options = { day: 'numeric', month: 'long', year: 'numeric' };
    document.getElementById('nextDate').textContent = date.toLocaleDateString('id-ID', options);
}

// Inisialisasi awal (jika ada form validation error sebelumnya)
document.addEventListener("DOMContentLoaded", function() {
    // Re-check UI for previously selected items (old input)
    document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
        cb.closest('.item-row').classList.add('selected');
    });
    
    updatePreviewNext();
    hitungTerpilih();
    hitungTerpilihUser();
});

setTimeout(() => {
    const a = document.getElementById('flashAlert');
    if (a) a.style.opacity = '0', setTimeout(() => a.remove(), 400);
}, 5000);
</script>
@endpush