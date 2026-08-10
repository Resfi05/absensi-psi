@extends('layouts.admin')
@section('title', 'Edit User — ' . $user->name)
@section('page-title', 'Edit User')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-sep">/</span>
            <a href="{{ route('admin.users.index') }}" class="breadcrumb-link">Kelola User</a>
            <span class="breadcrumb-sep">/</span>
            <span class="breadcrumb-current">Edit: {{ $user->name }}</span>
        </nav>
        <h2 class="page-heading">Edit <span class="heading-accent">{{ $user->name }}</span></h2>
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
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
            @csrf @method('PUT')
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
                                   value="{{ old('name', $user->name) }}">
                            @error('name')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Username</label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                                   value="{{ old('username', $user->username) }}">
                            @error('username')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}">
                        @error('email')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label">Password Baru <span style="font-size:0.75rem;color:var(--text-muted);font-weight:400;">(kosongkan jika tidak diubah)</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Min. 6 karakter">
                            @error('password')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                   placeholder="Ulangi password baru">
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label required">Role</label>
                            <select name="role" class="form-control"
                                    onchange="toggleSpesialisasi(this.value)">
                                <option value="admin" {{ old('role',$user->role)==='admin' ? 'selected':'' }}>Admin</option>
                                <option value="user"  {{ old('role',$user->role)==='user'  ? 'selected':'' }}>Petugas</option>
                            </select>
                        </div>

                        {{-- 🔥 REVISI: Spesialisasi menjadi Checkbox Grid & Tarik Data Lama --}}
                        <div class="form-group" id="spesialisasiField" style="{{ (old('role',$user->role)==='admin') ? 'display:none' : '' }}">
                            <label class="form-label">Spesialisasi <span style="font-size:0.75rem;font-weight:400;color:var(--text-muted);">(Bisa pilih lebih dari satu)</span></label>
                            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 8px; margin-top: 4px;">
                                @php
                                    // Ambil array ID spesialisasi lama dari database
                                    $selectedSpesialisasi = old('spesialisasi_id', $user->spesialisasi->pluck('id')->toArray());
                                @endphp
                                @foreach($kategori_list as $k)
                                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; background:#f8fafc; padding:8px 12px; border-radius:6px; border:1px solid var(--border);">
                                        <input type="checkbox" name="spesialisasi_id[]" value="{{ $k->id }}"
                                               {{ in_array($k->id, $selectedSpesialisasi) ? 'checked' : '' }}
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
                               value="{{ old('no_hp', $user->no_hp) }}" placeholder="08xxxxxxxxxx (10-14 digit)">
                        @error('no_hp')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="toggle-group">
                        <div class="toggle-info">
                            <div class="toggle-label">Status Aktif</div>
                            <div class="toggle-desc">User aktif dapat login ke sistem</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', $user->is_active ? '1' : '') === '1' ? 'checked':'' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div style="display:flex;gap:10px;padding-top:4px;">
                        <button type="submit" class="btn-primary">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v14a2 2 0 01-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                            </svg>
                            Simpan Perubahan
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
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                Profil Saat Ini
            </div>
            <div class="form-card-body" style="gap:10px;">
                <div style="text-align:center;padding:12px 0;">
                    
                    {{-- 🔥 REVISI: Tampilkan Foto Profil Jika Ada --}}
                    @if($user->foto)
                        <div style="width:64px;height:64px;border-radius:50%;margin:0 auto 10px;overflow:hidden;border:2px solid var(--border);box-shadow:0 2px 4px rgba(0,0,0,0.05);">
                            <img src="{{ Storage::url($user->foto) }}" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">
                        </div>
                    @else
                        <div style="width:56px;height:56px;border-radius:50%;background:{{ $user->role === 'admin' ? 'linear-gradient(135deg,#7c3aed,#a855f7)' : 'linear-gradient(135deg,#1e40af,#3b82f6)' }};color:white;display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:700;margin:0 auto 10px;">
                            {{ strtoupper(substr($user->name,0,1)) }}
                        </div>
                    @endif
                    
                    <div style="font-weight:700;color:var(--text-main);">{{ $user->name }}</div>
                    <div style="font-size:0.8rem;color:var(--text-muted);">{{ $user->email }}</div>
                </div>
                
                <div style="background:#f8fafc;border-radius:8px;padding:10px 12px;">
                    <div style="font-size:0.75rem;color:var(--text-muted);margin-bottom:6px;">Spesialisasi Saat Ini</div>
                    @if($user->spesialisasi && $user->spesialisasi->count() > 0)
                        <div style="display:flex; flex-wrap:wrap; gap:6px;">
                            @foreach($user->spesialisasi as $sp)
                                <span class="badge-jenis" style="background:{{ $sp->warna }}20;color:{{ $sp->warna }};border:1px solid {{ $sp->warna }}40; font-size:0.72rem; padding:3px 8px;">
                                    {{ $sp->nama }}
                                </span>
                            @endforeach
                        </div>
                    @elseif($user->role === 'admin')
                        <span style="font-size:0.82rem;color:var(--text-muted);">-</span>
                    @else
                        <span style="font-size:0.82rem;color:var(--text-muted);">Belum diset</span>
                    @endif
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