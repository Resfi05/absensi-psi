@extends('layouts.admin')
@section('title', 'Kelola User')
@section('page-title', 'Kelola User')
@section('content')

{{-- 🗑️ FORM HIDDEN UNTUK BULK DELETE 🗑️ --}}
<form id="bulkDeleteForm" method="POST" action="{{ route('admin.users.bulk-delete') }}" style="display: none;">
    @csrf
</form>

<div class="page-header">
    <div class="page-header-left">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-sep">/</span>
            <span class="breadcrumb-current">Kelola User</span>
        </nav>
        <h2 class="page-heading">Kelola <span class="heading-accent">User</span></h2>
    </div>
    
    <div style="display:flex; gap:10px;">
        {{-- TOMBOL HAPUS MASSAL --}}
        <button type="button" id="btnBulkDelete" onclick="submitBulkDelete()" class="btn-primary" style="display:none; background-color:#ef4444; border-color:#ef4444; box-shadow:0 4px 12px rgba(239, 68, 68, 0.2);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                <path d="M10 11v6M14 11v6"/>
            </svg>
            Hapus Terpilih (<span id="bulkCount">0</span>)
        </button>

        <a href="{{ route('admin.users.create') }}" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Tambah User
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
@if(session('error'))
<div class="alert alert-error" id="flashAlert">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
    </svg>
    {{ session('error') }}
    <button class="alert-close" onclick="this.parentElement.remove()">✕</button>
</div>
@endif

<div class="card">
    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="filter-form">
        <div class="filter-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" name="search" placeholder="Cari nama, username, atau email..."
                   value="{{ request('search') }}">
        </div>

        <select name="role" class="filter-select" onchange="toggleSpesialisasiFilter(this.value)">
            <option value="semua" {{ !request('role') || request('role')==='semua' ? 'selected':'' }}>Semua Role</option>
            <option value="admin" {{ request('role')==='admin' ? 'selected':'' }}>Admin</option>
            <option value="user"  {{ request('role')==='user'  ? 'selected':'' }}>Petugas</option>
        </select>

        <select name="spesialisasi" class="filter-select" id="filterSpesialisasi">
            <option value="">Semua Spesialisasi</option>
            @foreach($kategori_list as $k)
                <option value="{{ $k->id }}" {{ request('spesialisasi') == $k->id ? 'selected':'' }}>
                    {{ $k->nama }}
                </option>
            @endforeach
        </select>

        <select name="status" class="filter-select">
            <option value="" {{ !request('status') ? 'selected':'' }}>Semua Status</option>
            <option value="aktif"    {{ request('status')==='aktif'    ? 'selected':'' }}>Aktif</option>
            <option value="nonaktif" {{ request('status')==='nonaktif' ? 'selected':'' }}>Nonaktif</option>
        </select>

        <button type="submit" class="btn-filter">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            Cari
        </button>

        @if(request()->hasAny(['search','role','spesialisasi','status']))
        <a href="{{ route('admin.users.index') }}" class="btn-reset">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>
            </svg>
            Reset
        </a>
        @endif
    </form>

    {{-- Table Info --}}
    <div class="table-info">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
        </svg>
        Total User: <strong>{{ $users->total() }}</strong>
    </div>

    {{-- Tabel --}}
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">
                        <input type="checkbox" id="selectAllCheckbox" style="cursor: pointer; width: 16px; height: 16px;">
                    </th>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Spesialisasi</th>
                    <th>No. HP</th>
                    <th>Status</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $i => $user)
                <tr>
                    <td style="text-align: center;">
                        @php
                            $isSelf = $user->id === auth()->id();
                            $hasHistory = $user->pengecekan_count > 0;
                            $isDisabled = $isSelf || $hasHistory;
                            $titleText = $isSelf ? "Tidak bisa menghapus akun sendiri" : ($hasHistory ? "Tidak bisa dihapus karena memiliki riwayat kerja" : "");
                        @endphp
                        
                        @if(!$isDisabled)
                            <input type="checkbox" class="row-checkbox" value="{{ $user->id }}" style="cursor: pointer; width: 16px; height: 16px;">
                        @else
                            <input type="checkbox" disabled title="{{ $titleText }}" style="opacity: 0.4; cursor: not-allowed; width: 16px; height: 16px;">
                        @endif
                    </td>
                    <td class="text-muted text-sm">{{ $users->firstItem() + $i }}</td>
                    <td>
                        <div class="user-cell">
                            @if($user->foto)
                                <div class="user-avatar-sm" style="overflow:hidden; display:flex; align-items:center; justify-content:center; padding:0;">
                                    <img src="{{ Storage::url($user->foto) }}" alt="Avatar" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                                </div>
                            @else
                                <div class="user-avatar-sm"
                                     style="background:{{ $user->role === 'admin' ? 'linear-gradient(135deg,#7c3aed,#a855f7)' : 'linear-gradient(135deg,#1e40af,#3b82f6)' }}">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif

                            <div>
                                <div class="user-name-cell">{{ $user->name }}</div>
                                <div class="user-email-cell">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="code-text">{{ $user->username }}</span></td>
                    <td>
                        <span class="badge-role {{ $user->role === 'admin' ? 'badge-purple' : 'badge-blue' }}">
                            {{ $user->role === 'admin' ? 'Admin' : 'Petugas' }}
                        </span>
                    </td>
                    <td>
                        {{-- 🔥 REVISI: Tampilkan lebih dari satu spesialisasi atau Kosong jika Admin --}}
                        @if($user->role === 'admin')
                            <span style="font-size:0.75rem;color:var(--text-muted);">-</span>
                        @elseif($user->spesialisasi && $user->spesialisasi->count() > 0)
                            <div style="display:flex; flex-wrap:wrap; gap:4px;">
                                @foreach($user->spesialisasi as $sp)
                                    <span class="badge-jenis" style="background:{{ $sp->warna }}20; color:{{ $sp->warna }}; border:1px solid {{ $sp->warna }}40; font-size:0.7rem; padding:2px 6px;">
                                        {{ $sp->nama }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="badge-status badge-orange" style="font-size:0.7rem;">Belum diset</span>
                        @endif
                    </td>
                    <td class="text-sm text-muted">{{ $user->no_hp ?? '-' }}</td>
                    <td>
                        <span class="badge-status {{ $user->is_active ? 'badge-green' : 'badge-red' }}">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>
                        <div class="action-btns" style="justify-content:center;">
                            <a href="{{ route('admin.users.edit', $user->id) }}"
                               class="btn-icon btn-icon-blue" title="Edit">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </a>
                            @if(!$isSelf)
                            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                                  onsubmit="return confirm('Hapus user {{ $user->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon btn-icon-red" title="Hapus">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                                        <path d="M10 11v6M14 11v6"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-table" style="padding:40px">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                            </svg>
                            <p>Tidak ada user ditemukan</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Menampilkan {{ $users->firstItem() }} sampai {{ $users->lastItem() }} dari {{ $users->total() }} user
        </div>
        <div class="pagination-links">
            @if($users->onFirstPage())
                <span class="page-btn page-btn-disabled">«</span>
            @else
                <a href="{{ $users->previousPageUrl() }}" class="page-btn">«</a>
            @endif
            @foreach($users->getUrlRange(max(1,$users->currentPage()-2),min($users->lastPage(),$users->currentPage()+2)) as $page => $url)
                @if($page == $users->currentPage())
                    <span class="page-btn page-btn-active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                @endif
            @endforeach
            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" class="page-btn">»</a>
            @else
                <span class="page-btn page-btn-disabled">»</span>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
// LOGIKA CHECKBOX HAPUS MASSAL
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

    if (confirm(`Anda yakin ingin menghapus ${checkedBoxes.length} user yang dipilih secara massal?\n\n(Tindakan ini tidak dapat dikembalikan)`)) {
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

function toggleSpesialisasiFilter(role) {
    const sel = document.getElementById('filterSpesialisasi');
    sel.style.display = role === 'admin' ? 'none' : '';
}
// Init
toggleSpesialisasiFilter('{{ request("role","semua") }}');

setTimeout(() => {
    const a = document.getElementById('flashAlert');
    if (a) a.style.opacity = '0', setTimeout(() => a.remove(), 400);
}, 4000);
</script>
@endpush