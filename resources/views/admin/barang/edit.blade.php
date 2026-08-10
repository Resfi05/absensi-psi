@extends('layouts.admin')
@section('title', 'Edit Barang — ' . $barang->kode_barang)
@section('page-title', 'Edit Barang')
@section('content')

<div class="page-header">
    <div class="page-header-left">
        <nav class="breadcrumb-nav">
            <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span class="breadcrumb-sep">›</span>
            <a href="{{ route('admin.barang.index') }}" class="breadcrumb-link">Kelola Barang</a>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-current">Edit: {{ $barang->kode_barang }}</span>
        </nav>
        <h2 class="page-heading">Edit <span class="heading-accent">{{ $barang->kode_barang }}</span></h2>
    </div>
    <a href="{{ route('admin.barang.index') }}" class="btn-secondary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
        </svg>
        Kembali
    </a>
</div>

<form method="POST" action="{{ route('admin.barang.update', $barang->id) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="form-grid">
        <div class="form-col-main">
            <div class="form-card">
                <div class="form-card-header">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="3" width="20" height="14" rx="2"/>
                        <line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
                    </svg>
                    Informasi Dasar
                </div>
                <div class="form-card-body">

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label required">Kode Barang</label>
                            <input type="text" name="kode_barang"
                                   class="form-control @error('kode_barang') is-invalid @enderror"
                                   value="{{ old('kode_barang', $barang->kode_barang) }}"
                                   oninput="this.value=this.value.toUpperCase()">
                            @error('kode_barang')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Kategori Barang</label>
                            <select name="kategori_id" id="kategoriSelect"
                                    class="form-control @error('kategori_id') is-invalid @enderror">
                                <option value="" data-nama="">-- Pilih Kategori --</option>
                                @foreach($kategori_list as $k)
                                    <option value="{{ $k->id }}" data-nama="{{ $k->nama }}"
                                            {{ old('kategori_id', $barang->kategori_id) == $k->id ? 'selected':'' }}>
                                        {{ $k->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kategori_id')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label required">Nama Barang</label>
                            <input type="text" name="nama_barang"
                                   class="form-control @error('nama_barang') is-invalid @enderror"
                                   value="{{ old('nama_barang', $barang->nama_barang) }}">
                            @error('nama_barang')<span class="form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" id="labelExpired">Tanggal Kedaluwarsa (Expired)</label>
                            <input type="date" name="tanggal_expired" id="inputExpired"
                                   class="form-control @error('tanggal_expired') is-invalid @enderror"
                                   value="{{ old('tanggal_expired', $barang->tanggal_expired ? \Carbon\Carbon::parse($barang->tanggal_expired)->format('Y-m-d') : '') }}">
                            @error('tanggal_expired')<span class="form-error">{{ $message }}</span>@enderror
                            
                            <div id="infoExpiredApar" style="display:none; margin-top:8px; background:#eff6ff; border:1px solid #bfdbfe; border-radius:6px; padding:10px 12px; display:flex; gap:8px; align-items:flex-start;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" style="width:16px; height:16px; flex-shrink:0; margin-top:2px;">
                                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                                </svg>
                                <span style="font-size:0.75rem; color:#1e40af; line-height:1.4;">
                                    Karena Anda memilih kategori <strong>APAR</strong>, sistem mewajibkan pengisian Tanggal Expired untuk kebutuhan notifikasi perawatan.
                                </span>
                            </div>
                            <span class="form-hint" id="hintExpiredDefault">Khusus barang bermasa pakai. Kosongkan jika tidak ada.</span>
                        </div>
                    </div>

                    <hr style="border-top:1px dashed var(--border); margin:20px 0;">
                    
                    <h4 style="font-size:0.9rem; font-weight:700; color:var(--text-main); margin-bottom:14px;">Detail Spesifikasi</h4>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label">Merk</label>
                            <input type="text" name="merk" class="form-control @error('merk') is-invalid @enderror" value="{{ old('merk', $barang->merk) }}" placeholder="Contoh: Panasonic, Daikin, dll">
                            @error('merk')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Model</label>
                            <input type="text" name="model" class="form-control @error('model') is-invalid @enderror" value="{{ old('model', $barang->model) }}" placeholder="Contoh: Inverter, Split, dll">
                            @error('model')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label">Tipe</label>
                            <input type="text" name="tipe" class="form-control @error('tipe') is-invalid @enderror" value="{{ old('tipe', $barang->tipe) }}" placeholder="Contoh: CS-XU10XKP, dll">
                            @error('tipe')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Keterangan Tambahan</label>
                            <textarea name="keterangan" class="form-control form-textarea @error('keterangan') is-invalid @enderror" rows="2" placeholder="Catatan tambahan lainnya...">{{ old('keterangan', $barang->keterangan) }}</textarea>
                            @error('keterangan')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                </div>
            </div>

            <div class="form-card">
                <div class="form-card-header">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    Lokasi Barang
                </div>
                <div class="form-card-body">
                    <div class="form-group">
                        <label class="form-label required">Pilih Lokasi</label>
                        <select name="lokasi_id"
                                class="form-control @error('lokasi_id') is-invalid @enderror">
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach($lokasi_list as $lok)
                                <option value="{{ $lok->id }}"
                                        {{ old('lokasi_id', $barang->lokasi_id) == $lok->id ? 'selected':'' }}>
                                    {{ $lok->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('lokasi_id')<span class="form-error">{{ $message }}</span>@enderror

                        @if(!$barang->lokasi_id && $barang->lokasi)
                        <div style="background:#fef3c7;border-radius:8px;padding:10px 12px;margin-top:8px;font-size:0.78rem;color:#d97706;">
                            ⚠️ Barang ini masih pakai lokasi lama (teks bebas): <strong>"{{ $barang->lokasi }}"</strong>.
                            Silakan pilih lokasi yang sesuai dari master di atas.
                        </div>
                        @endif

                        <span class="form-hint">
                            Lokasi belum tersedia?
                            <a href="{{ route('admin.lokasi.index') }}" style="color:var(--blue-mid);">Tambah lokasi baru</a>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-col-side">
            <div class="form-card">
                <div class="form-card-header">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><path d="M8 12l2 2 4-4"/>
                    </svg>
                    Status
                </div>
                <div class="form-card-body">
                    <div class="toggle-group">
                        <div class="toggle-info">
                            <div class="toggle-label">Status Aktif</div>
                            <div class="toggle-desc">Barang aktif masuk jadwal pengecekan</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', $barang->is_active ? '1' : '') === '1' ? 'checked':'' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <div class="form-card-header">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                    </svg>
                    Foto Barang
                </div>
                <div class="form-card-body">
                    <div class="foto-upload-area" id="fotoArea" onclick="document.getElementById('fotoInput').click()">
                        <div class="foto-preview-wrap" id="fotoPreviewWrap"
                             style="{{ $barang->foto ? 'display:block' : 'display:none' }}">
                            <img id="fotoPreview"
                                 src="{{ $barang->foto ? Storage::url($barang->foto) : '' }}"
                                 alt="Preview">
                            <button type="button" class="foto-remove"
                                    onclick="event.stopPropagation();removeFoto()">✕</button>
                        </div>
                        <div class="foto-placeholder" id="fotoPlaceholder"
                             style="{{ $barang->foto ? 'display:none' : 'display:flex' }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <polyline points="21 15 16 10 5 21"/>
                            </svg>
                            <p>Klik untuk ganti foto</p>
                            <span>JPG, PNG, WebP • Max 2MB</span>
                        </div>
                    </div>
                    <input type="file" name="foto" id="fotoInput" accept="image/*" style="display:none"
                           onchange="previewFoto(this)">
                    @error('foto')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-actions" style="flex-direction:column;gap:10px;">
                <button type="submit" class="btn-primary btn-full">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v14a2 2 0 01-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                    </svg>
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.barang.index') }}" class="btn-secondary btn-full">Batal</a>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const kategoriSelect = document.getElementById('kategoriSelect');
    const labelExpired = document.getElementById('labelExpired');
    const inputExpired = document.getElementById('inputExpired');
    const infoExpiredApar = document.getElementById('infoExpiredApar');
    const hintExpiredDefault = document.getElementById('hintExpiredDefault');

    function checkExpiredRequirement() {
        const selectedOption = kategoriSelect.options[kategoriSelect.selectedIndex];
        if (!selectedOption) return; 
        
        const kategoriNama = selectedOption.getAttribute('data-nama') || '';
        
        if (kategoriNama.toUpperCase().includes('APAR')) {
            labelExpired.classList.add('required');
            inputExpired.setAttribute('required', 'required');
            infoExpiredApar.style.display = 'flex';
            hintExpiredDefault.style.display = 'none';
        } else {
            labelExpired.classList.remove('required');
            inputExpired.removeAttribute('required');
            infoExpiredApar.style.display = 'none';
            hintExpiredDefault.style.display = 'block';
        }
    }

    kategoriSelect.addEventListener('change', checkExpiredRequirement);
    checkExpiredRequirement();
});

function previewFoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('fotoPreview').src = e.target.result;
            document.getElementById('fotoPreviewWrap').style.display = 'block';
            document.getElementById('fotoPlaceholder').style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function removeFoto() {
    document.getElementById('fotoInput').value = '';
    document.getElementById('fotoPreviewWrap').style.display = 'none';
    document.getElementById('fotoPlaceholder').style.display = 'flex';
}
</script>
@endpush