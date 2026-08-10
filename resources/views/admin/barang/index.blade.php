@extends('layouts.admin')

@section('title', 'Kelola Data Barang')
@section('page-title', 'Kelola Data Barang')

@section('content')

{{-- FORM HIDDEN UNTUK BULK DELETE --}}
<form id="bulkDeleteForm" method="POST" action="{{ route('admin.barang.bulk-delete') }}" style="display: none;">
    @csrf
</form>

<div class="page-header">
    <div class="page-header-left">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-current">Kelola Barang</span>
        </nav>
        <h2 class="page-heading">Kelola <span class="heading-accent">Data Barang</span></h2>
    </div>
    
    <div style="display:flex; gap:10px;">
        <button type="button" id="btnBulkDelete" onclick="submitBulkDelete()" class="btn-primary" style="display:none; background-color:#ef4444; border-color:#ef4444; box-shadow:0 4px 12px rgba(239, 68, 68, 0.2);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                <path d="M10 11v6M14 11v6"/>
            </svg>
            Hapus Terpilih (<span id="bulkCount">0</span>)
        </button>

        <a href="{{ route('admin.barang.create') }}" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Tambah Barang
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" id="flashAlert">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    {{ session('success') }}
    <button class="alert-close" onclick="this.parentElement.remove()">×</button>
</div>
@endif

{{-- TAB KATEGORI --}}
<div class="tab-bar">
    <a href="{{ request()->fullUrlWithQuery(['kategori' => 'semua', 'page' => 1]) }}"
       class="tab-item {{ !request('kategori') || request('kategori') === 'semua' ? 'tab-active' : '' }}">
        <span>Semua</span>
        <span class="tab-badge">{{ $total_all }}</span>
    </a>
    @foreach($kategori_list as $k)
    <a href="{{ request()->fullUrlWithQuery(['kategori' => $k->id, 'page' => 1]) }}"
       class="tab-item {{ request('kategori') == $k->id ? 'tab-active' : '' }}">
        <span>{{ $k->nama }}</span>
        <span class="tab-badge" style="{{ request('kategori') == $k->id ? 'background:rgba(255,255,255,0.25)' : 'background:'.$k->warna.'20;color:'.$k->warna }}">
            {{ $k->barang()->count() }}
        </span>
    </a>
    @endforeach
</div>

{{-- FILTER BAR --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('admin.barang.index') }}" class="filter-form">
        @if(request('kategori'))
            <input type="hidden" name="kategori" value="{{ request('kategori') }}">
        @endif

        <div class="search-input-wrap">
            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" name="search" placeholder="Cari kode / nama barang / lokasi..."
                   value="{{ request('search') }}" class="filter-input search-field">
        </div>

        <select name="lokasi" class="filter-select">
            <option value="">Semua Lokasi</option>
            @foreach($lokasi_list as $lok)
                <option value="{{ $lok->id }}" {{ request('lokasi') == $lok->id ? 'selected':'' }}>{{ $lok->nama }}</option>
            @endforeach
        </select>

        <select name="status" class="filter-select">
            <option value="">Semua Status</option>
            <option value="aktif"    {{ request('status')==='aktif'    ? 'selected':'' }}>Aktif</option>
            <option value="nonaktif" {{ request('status')==='nonaktif' ? 'selected':'' }}>Tidak Aktif</option>
        </select>

        <button type="submit" class="btn-filter">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            Cari
        </button>

        @if(request()->hasAny(['search','lokasi','status']))
        <a href="{{ route('admin.barang.index', ['kategori' => request('kategori')]) }}" class="btn-reset">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>
            </svg>
            Reset
        </a>
        @endif
    </form>

    <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn-export">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/>
        </svg>
        Export Excel
    </a>
</div>

{{-- TABLE --}}
<div class="card">
    <div class="card-header">
        <div class="total-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Total Barang: <strong>{{ $barang->total() }}</strong>
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table barang-table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">
                        <input type="checkbox" id="selectAllCheckbox" style="cursor: pointer; width: 16px; height: 16px;">
                    </th>
                    <th style="width:50px">No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Lokasi</th>
                    <th style="width:110px">Status</th>
                    <th style="width:90px;text-align:center">QR Code</th>
                    <th style="width:120px;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barang as $i => $item)
                <tr>
                    <td style="text-align: center;">
                        @if($item->jadwal_count == 0)
                            <input type="checkbox" class="row-checkbox" value="{{ $item->id }}" style="cursor: pointer; width: 16px; height: 16px;">
                        @else
                            <input type="checkbox" disabled title="Barang tidak bisa dihapus massal karena terikat dengan jadwal/riwayat pengecekan" style="opacity: 0.4; cursor: not-allowed; width: 16px; height: 16px;">
                        @endif
                    </td>
                    <td class="text-muted text-sm">{{ $barang->firstItem() + $i }}</td>
                    <td>
                        <span class="kode-barang">{{ $item->kode_barang }}</span>
                    </td>
                    <td>
                        <div class="barang-nama-wrap" style="display: flex; align-items: center; gap: 10px;">
                            @if($item->foto)
                                <img src="{{ Storage::url($item->foto) }}" alt="{{ $item->nama_barang }}" class="barang-thumb">
                            @else
                                {{-- 🔥 IKON KUBUS 3D PERSIS SEPERTI DI DASHBOARD 🔥 --}}
                                @php
                                    $catColor = $item->kategori?->warna ?? '#2563eb';
                                @endphp
                                <div class="barang-thumb-placeholder"
                                     style="width: 40px; height: 40px; min-width: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: {{ $catColor }}20; color: {{ $catColor }};">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 22px; height: 22px;">
                                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <div class="barang-nama">{{ $item->nama_barang }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($item->kategori)
                            <span class="badge-jenis"
                                  style="background:{{ $item->kategori->warna }}20;color:{{ $item->kategori->warna }};border:1px solid {{ $item->kategori->warna }}40;">
                                {{ $item->kategori->nama }}
                            </span>
                        @else
                            <span class="badge-jenis badge-gray">{{ $item->jenis_barang }}</span>
                        @endif
                    </td>
                    
                    <td style="max-width: 220px; vertical-align: top; padding-top: 14px;">
                        <div style="display: flex; align-items: flex-start; gap: 6px; line-height: 1.4;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; color: #94a3b8; flex-shrink: 0; margin-top: 1px;">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <span style="word-break: break-word; font-size: 0.85rem; color: var(--text-main);">
                                @if($item->lokasiRelasi)
                                    {{ $item->lokasiRelasi->nama }}
                                @else
                                    <span style="color:#d97706;">{{ $item->lokasi ?? '—' }} <span style="font-size:0.68rem;">(lama)</span></span>
                                @endif
                            </span>
                        </div>
                    </td>

                    <td>
                        <span class="badge-status {{ $item->is_active ? 'badge-green' : 'badge-red' }}">
                            {{ $item->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($item->qrCode)
                            <button class="btn-qr" title="Lihat QR Code"
                                    onclick="showQR('{{ $item->kode_barang }}','{{ $item->nama_barang }}','{{ route('admin.qrcode.cetak',$item->qrCode->id) }}','{{ asset('storage/'.$item->qrCode->qr_code_path) }}')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                                    <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="3" height="3"/>
                                </svg>
                            </button>
                        @else
                            <a href="{{ route('admin.qrcode.index') }}" class="btn-qr-generate" title="Generate QR">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                            </a>
                        @endif
                    </td>
                    <td>
                        <div class="aksi-group">
                            <a href="{{ route('admin.barang.edit', $item->id) }}"
                               class="btn-aksi btn-aksi-edit" title="Edit">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </a>
                            <a href="{{ route('admin.barang.show', $item->id) }}"
                               class="btn-aksi btn-aksi-view" title="Detail">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('admin.barang.destroy', $item->id) }}"
                                  onsubmit="return confirmDelete('{{ $item->nama_barang }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-aksi btn-aksi-delete" title="Hapus">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                                        <path d="M10 11v6M14 11v6"/>
                                        <path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-table-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <rect x="2" y="3" width="20" height="14" rx="2"/>
                            </svg>
                            <p>Belum ada data barang</p>
                            <a href="{{ route('admin.barang.create') }}" class="btn-primary btn-sm">+ Tambah Barang</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($barang->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Menampilkan {{ $barang->firstItem() }} sampai {{ $barang->lastItem() }} dari {{ $barang->total() }} data
        </div>
        <div class="pagination-links">
            @if($barang->onFirstPage())
                <span class="page-btn page-btn-disabled">««</span>
                <span class="page-btn page-btn-disabled">‹</span>
            @else
                <a href="{{ $barang->url(1) }}" class="page-btn">««</a>
                <a href="{{ $barang->previousPageUrl() }}" class="page-btn">‹</a>
            @endif
            @foreach($barang->getUrlRange(max(1,$barang->currentPage()-2),min($barang->lastPage(),$barang->currentPage()+2)) as $page => $url)
                @if($page == $barang->currentPage())
                    <span class="page-btn page-btn-active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                @endif
            @endforeach
            @if($barang->hasMorePages())
                <a href="{{ $barang->nextPageUrl() }}" class="page-btn">›</a>
                <a href="{{ $barang->url($barang->lastPage()) }}" class="page-btn">»»</a>
            @else
                <span class="page-btn page-btn-disabled">›</span>
                <span class="page-btn page-btn-disabled">»»</span>
            @endif

            <select class="per-page-select" onchange="changePerPage(this.value)">
                @foreach([8,10,25,50] as $pp)
                    <option value="{{ $pp }}" {{ request('per_page',10)==$pp ? 'selected':'' }}>{{ $pp }}/hal</option>
                @endforeach
            </select>
        </div>
    </div>
    @endif
</div>

{{-- QR MODAL --}}
<div class="modal-overlay" id="qrModal" onclick="closeQR()">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="modal-header">
            <div>
                <div class="modal-title" id="qrTitle">—</div>
                <div class="modal-sub" id="qrSub">—</div>
            </div>
            <button class="modal-close" onclick="closeQR()">×</button>
        </div>
        <div class="modal-body">
            <a href="#" id="qrCetakImageLink" target="_blank">
                <img id="qrModalImage" src="" alt="QR Code"
                     style="width:180px;height:180px;object-fit:contain;border-radius:8px;background:#f8fafc;padding:8px;cursor:pointer;">
            </a>
        </div>
        <div class="modal-footer">
            <a href="#" id="qrCetakLink" target="_blank" class="btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"/>
                    <path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
                Cetak QR Code
            </a>
            <button class="btn-secondary" onclick="closeQR()">Tutup</button>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.barang-thumb {
    width: 40px;
    height: 40px;
    min-width: 40px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid var(--border);
}
.barang-thumb-placeholder {
    width: 40px;
    height: 40px;
    min-width: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush

@push('scripts')
<script>
const selectAllCheckbox = document.getElementById('selectAllCheckbox');
const rowCheckboxes     = document.querySelectorAll('.row-checkbox');
const btnBulkDelete     = document.getElementById('btnBulkDelete');
const bulkCountSpan     = document.getElementById('bulkCount');
const bulkDeleteForm    = document.getElementById('bulkDeleteForm');

function updateBulkButtonState() {
    const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
    if (checkedCount > 0) {
        btnBulkDelete.style.display = 'inline-flex';
        bulkCountSpan.textContent = checkedCount;
    } else {
        btnBulkDelete.style.display = 'none';
    }
}

if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function() {
        rowCheckboxes.forEach(cb => {
            if (!cb.disabled) cb.checked = selectAllCheckbox.checked;
        });
        updateBulkButtonState();
    });
}

rowCheckboxes.forEach(cb => {
    cb.addEventListener('change', function() {
        if (!this.checked && selectAllCheckbox) selectAllCheckbox.checked = false;
        updateBulkButtonState();
    });
});

function submitBulkDelete() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    if (checkedBoxes.length === 0) return;

    if (confirm(`Anda yakin ingin menghapus ${checkedBoxes.length} barang yang dipilih secara massal?\n\n(Tindakan ini tidak dapat dikembalikan)`)) {
        bulkDeleteForm.innerHTML = '@csrf'; 
        
        checkedBoxes.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = cb.value;
            bulkDeleteForm.appendChild(input);
        });

        bulkDeleteForm.submit();
    }
}

function showQR(kode, nama, cetakUrl, imagePath) {
    document.getElementById('qrTitle').textContent = kode;
    document.getElementById('qrSub').textContent   = nama;
    document.getElementById('qrCetakLink').href        = cetakUrl;
    document.getElementById('qrCetakImageLink').href   = cetakUrl;
    document.getElementById('qrModalImage').src        = imagePath;
    document.getElementById('qrModal').classList.add('active');
}

function closeQR() { document.getElementById('qrModal').classList.remove('active'); }

function confirmDelete(nama) { return confirm(`Yakin hapus barang "${nama}"?\nData tidak dapat dikembalikan.`); }

function changePerPage(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', val);
    url.searchParams.set('page', 1);
    window.location.href = url.toString();
}

setTimeout(() => {
    const a = document.getElementById('flashAlert');
    if (a) a.style.opacity = '0', setTimeout(() => a.remove(), 400);
}, 4000);
</script>
@endpush