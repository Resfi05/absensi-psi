@extends('layouts.admin')
@section('title', 'Tambah User')
@section('page-title', 'Tambah User')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-sep">/</span>
            <a href="{{ route('admin.users.index') }}" class="breadcrumb-link">Kelola User</a>
            <span class="breadcrumb-sep">/</span>
            <span class="breadcrumb-current">Tambah User</span>
        </nav>
        <h2 class="page-heading">Tambah <span class="heading-accent">User Baru</span></h2>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn-secondary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
        </svg>
        Kembali
    </a>
</div>

<div class="form-grid">
    <div class="form-col-main">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="form-card">
                <div class="form-card-header">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    Informasi User
                </div>
                <div class="form-card-body">

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label required">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="Nama lengkap">
                            @error('name')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Username</label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                                   value="{{ old('username') }}" placeholder="username">
                            @error('username')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="email@perusahaan.com">
                        @error('email')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label required">Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Min. 6 karakter">
                            @error('password')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                   placeholder="Ulangi password">
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label required">Role</label>
                            <select name="role" class="form-control @error('role') is-invalid @enderror"
                                    onchange="toggleSpesialisasi(this.value)">
                                <option value="">-- Pilih Role --</option>
                                <option value="admin" {{ old('role')==='admin' ? 'selected':'' }}>Admin</option>
                                <option value="user"  {{ old('role')==='user'  ? 'selected':'' }}>Petugas</option>
                            </select>
                            @error('role')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        {{-- 🔥 REVISI: Spesialisasi menjadi Checkbox Grid --}}
                        <div class="form-group" id="spesialisasiField" style="{{ old('role')==='admin' ? 'display:none' : '' }}">
                            <label class="form-label">Spesialisasi <span style="font-size:0.75rem;font-weight:400;color:var(--text-muted);">(Bisa pilih lebih dari satu)</span></label>
                            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 8px; margin-top: 4px;">
                                @foreach($kategori_list as $k)
                                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; background:#f8fafc; padding:8px 12px; border-radius:6px; border:1px solid var(--border);">
                                        <input type="checkbox" name="spesialisasi_id[]" value="{{ $k->id }}"
                                               {{ in_array($k->id, old('spesialisasi_id', [])) ? 'checked' : '' }}
                                               style="width:16px; height:16px; accent-color:var(--primary);">
                                        <span style="font-size:0.85rem; font-weight:600; color:var(--text-main);">{{ $k->nama }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('spesialisasi_id')<span class="form-error">{{ $message }}</span>@enderror
                            <span class="form-hint" style="margin-top:6px; display:block;">Keahlian petugas — menentukan jadwal yang diterima</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror"
                               value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx (10-14 digit)">
                        @error('no_hp')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="toggle-group">
                        <div class="toggle-info">
                            <div class="toggle-label">Status Aktif</div>
                            <div class="toggle-desc">User aktif dapat login ke sistem</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active','1') === '1' ? 'checked':'' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div style="display:flex;gap:10px;padding-top:4px;">
                        <button type="submit" class="btn-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v14a2 2 0 01-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                            </svg>
                            Simpan User
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn-secondary">Batal</a>
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
                Tentang Spesialisasi
            </div>
            <div class="form-card-body" style="gap:10px;">
                <p style="font-size:0.8rem;color:var(--text-muted);line-height:1.6;">
                    Spesialisasi menentukan jenis barang apa yang bisa dicek oleh petugas ini.
                </p>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    @foreach($kategori_list as $k)
                    <div style="display:flex;align-items:center;gap:8px;padding:8px 10px;background:#f8fafc;border-radius:8px;border:1px solid var(--border);">
                        <span style="width:10px;height:10px;border-radius:50%;background:{{ $k->warna }};flex-shrink:0;"></span>
                        <div>
                            <div style="font-size:0.82rem;font-weight:700;color:var(--text-main);">{{ $k->nama }}</div>
                            <div style="font-size:0.72rem;color:var(--text-muted);">{{ $k->deskripsi ?? '' }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function toggleSpesialisasi(role) {
    const field = document.getElementById('spesialisasiField');
    field.style.display = role === 'admin' ? 'none' : '';
}
</script>
@endpush