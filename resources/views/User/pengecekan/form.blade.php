@extends('layouts.user')
@section('title', 'Form Pengecekan')
@section('content')

<div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;">
    <a href="{{ route('user.jadwal.index') }}" style="width:36px;height:36px;border-radius:10px;background:var(--p-surface);display:flex;align-items:center;justify-content:center;box-shadow:var(--p-shadow);flex-shrink:0;">
        <svg viewBox="0 0 24 24" fill="none" stroke="var(--p-text)" stroke-width="2" style="width:18px;height:18px;"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    </a>
    <div>
        <h1 style="font-size:1.05rem;font-weight:800;color:var(--p-text);margin:0;">{{ $jadwal->barang->nama_barang }}</h1>
        <p style="font-size:0.78rem;color:var(--p-muted);margin:2px 0 0;">{{ $jadwal->barang->lokasiRelasi->nama ?? 'Lokasi belum diatur' }}</p>
    </div>
</div>

{{-- ── BLOK MENAMPILKAN ERROR VALIDASI ── --}}
@if(session('error'))
    <div style="background: #fee2e2; border: 1px solid #f87171; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 0.85rem; font-weight: 600;">
        ⚠️ {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div style="background: #fee2e2; border: 1px solid #f87171; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 0.85rem; font-weight: 600;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('user.pengecekan.submit', $jadwal->id) }}" enctype="multipart/form-data" id="pengecekanForm">
    @csrf

    {{-- ── STEP 1: FOTO KONDISI BARANG ────────────────────── --}}
    <div class="progress-card">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
            <span style="width:22px;height:22px;border-radius:50%;background:var(--p-primary);color:white;font-size:0.7rem;font-weight:700;display:flex;align-items:center;justify-content:center;">1</span>
            <span style="font-size:0.9rem;font-weight:700;color:var(--p-text);">Foto Kondisi Barang</span>
            <span style="font-size:0.68rem;color:var(--p-danger);font-weight:600;margin-left:auto;">Wajib</span>
        </div>
        <p style="font-size:0.74rem;color:var(--p-muted);margin:0 0 12px;line-height:1.5;">
            📷 Foto kondisi barang <strong>saat Anda tiba</strong> — tampilkan seluruh bagian barang dengan jelas.
        </p>

        <div id="photoBeforeArea" onclick="document.getElementById('photoBeforeInput').click()"
             style="border:2px dashed var(--p-border);border-radius:14px;height:170px;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;background:var(--p-bg);overflow:hidden;position:relative;">
            <div id="photoBeforePlaceholder" style="text-align:center;color:var(--p-faint);">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:36px;height:36px;margin-bottom:6px;"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
                <p style="font-size:0.78rem;margin:0;">Ketuk untuk ambil foto</p>
            </div>
            <img id="photoBeforePreview" style="display:none;width:100%;height:100%;object-fit:cover;">
        </div>
        <input type="file" name="photo_before" id="photoBeforeInput" accept="image/*" capture="environment" style="display:none" required>
    </div>

    {{-- ── STEP 2: CHECKLIST ───────────────────────── --}}
    <div class="progress-card">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
            <span style="width:22px;height:22px;border-radius:50%;background:var(--p-primary);color:white;font-size:0.7rem;font-weight:700;display:flex;align-items:center;justify-content:center;">2</span>
            <span style="font-size:0.9rem;font-weight:700;color:var(--p-text);">Checklist Pengecekan</span>
        </div>

        <div style="display:flex;flex-direction:column;gap:10px;">
            @foreach($checklistTemplate as $i => $item)
            <label class="checklist-tap" data-index="{{ $i }}">
                <input type="hidden" name="checklist[{{ $item }}]" value="1" id="checklistInput{{ $i }}">
                <div class="checklist-tap-check">
                    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" style="width:13px;height:13px;"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <span class="checklist-tap-label">{{ $item }}</span>
            </label>
            @endforeach
        </div>
    </div>

    {{-- ── STEP 3: STATUS ──────────────────────────── --}}
    <div class="progress-card">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
            <span style="width:22px;height:22px;border-radius:50%;background:var(--p-primary);color:white;font-size:0.7rem;font-weight:700;display:flex;align-items:center;justify-content:center;">3</span>
            <span style="font-size:0.9rem;font-weight:700;color:var(--p-text);">Status Hasil Pengecekan</span>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <label class="status-tap status-tap-aman" data-status="aman">
                <input type="radio" name="status" value="aman" checked style="display:none;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:26px;height:26px;margin-bottom:6px;"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span style="font-size:0.82rem;font-weight:700;">Aman</span>
            </label>
            <label class="status-tap status-tap-rusak" data-status="perlu_tindakan">
                <input type="radio" name="status" value="perlu_tindakan" style="display:none;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:26px;height:26px;margin-bottom:6px;"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <span style="font-size:0.82rem;font-weight:700;">Perlu Tindakan</span>
            </label>
        </div>
    </div>

    {{-- ── STEP 4: CATATAN + FOTO BUKTI TINDAK LANJUT ── --}}
    <div class="progress-card" id="rusakSection" style="display:none;">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
            <span style="width:22px;height:22px;border-radius:50%;background:var(--p-danger);color:white;font-size:0.7rem;font-weight:700;display:flex;align-items:center;justify-content:center;">!</span>
            <span style="font-size:0.9rem;font-weight:700;color:var(--p-text);">Detail Temuan</span>
        </div>

        <div style="margin-bottom:14px;">
            <label style="display:block;font-size:0.8rem;font-weight:600;color:var(--p-text);margin-bottom:7px;">Catatan Kerusakan</label>
            <textarea name="notes" id="notesInput" rows="3" placeholder="Contoh: Filter kotor, suara berdengung tidak normal..."
                      style="width:100%;padding:12px 14px;border-radius:12px;border:1px solid var(--p-border);font-size:0.85rem;font-family:inherit;resize:none;"></textarea>
        </div>

        <div>
            <label style="display:block;font-size:0.8rem;font-weight:600;color:var(--p-text);margin-bottom:4px;">
                Foto Bukti Tindak Lanjut <span style="color:var(--p-danger);">*</span>
            </label>
            <p style="font-size:0.72rem;color:var(--p-muted);margin:0 0 8px;line-height:1.5;">
                📷 Foto <strong>setelah Anda melakukan tindakan</strong> (bersihkan filter, perbaiki komponen, dll).
            </p>
            <div id="photoAfterArea" onclick="document.getElementById('photoAfterInput').click()"
                 style="border:2px dashed var(--p-border);border-radius:14px;height:150px;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;background:var(--p-bg);overflow:hidden;position:relative;">
                <div id="photoAfterPlaceholder" style="text-align:center;color:var(--p-faint);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:32px;height:32px;margin-bottom:6px;"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    <p style="font-size:0.76rem;margin:0;">Ketuk untuk ambil foto</p>
                </div>
                <img id="photoAfterPreview" style="display:none;width:100%;height:100%;object-fit:cover;">
            </div>
            <input type="file" name="photo_after" id="photoAfterInput" accept="image/*" capture="environment" style="display:none">
        </div>
    </div>

    <button type="submit" class="btn-block btn-primary-block" id="submitBtn" style="margin-bottom:24px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        Submit Pengecekan
    </button>
</form>

@endsection

@push('styles')
<style>
.checklist-tap { display: flex; align-items: center; gap: 12px; padding: 13px 14px; border-radius: 13px; border: 1.5px solid var(--p-border); cursor: pointer; transition: border-color 0.15s, background 0.15s; }
.checklist-tap.checked { border-color: var(--p-success); background: var(--p-success-lt); }
.checklist-tap.unchecked { border-color: var(--p-danger); background: var(--p-danger-lt); }
.checklist-tap-check { width: 24px; height: 24px; border-radius: 50%; background: var(--p-success); display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: background 0.15s; }
.checklist-tap.unchecked .checklist-tap-check { background: var(--p-danger); }
.checklist-tap-label { font-size: 0.85rem; font-weight: 600; color: var(--p-text); flex: 1; }

.status-tap { display: flex; flex-direction: column; align-items: center; padding: 18px 12px; border-radius: 14px; border: 2px solid var(--p-border); cursor: pointer; color: var(--p-faint); background: var(--p-surface); transition: all 0.15s; }
.status-tap-aman.selected { border-color: var(--p-success); background: var(--p-success-lt); color: var(--p-success); }
.status-tap-rusak.selected { border-color: var(--p-danger); background: var(--p-danger-lt); color: var(--p-danger); }

@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
@endpush

@push('scripts')
{{-- Library CDN untuk kompresi gambar otomatis --}}
<script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>

<script>
// ── Toggle Checklist Item ──
document.querySelectorAll('.checklist-tap').forEach(label => {
    label.classList.add('checked');
    label.addEventListener('click', function(e) {
        e.preventDefault();
        const idx = this.dataset.index;
        const input = document.getElementById('checklistInput' + idx);
        const isChecked = input.value === '1';

        input.value = isChecked ? '0' : '1';
        this.classList.toggle('checked', !isChecked);
        this.classList.toggle('unchecked', isChecked);
    });
});

// ── Status Selection ──
const statusTaps = document.querySelectorAll('.status-tap');
const rusakSection = document.getElementById('rusakSection');
const notesInput = document.getElementById('notesInput');
const photoAfterInput = document.getElementById('photoAfterInput');

function selectStatus(value) {
    statusTaps.forEach(t => {
        const isSelected = t.dataset.status === value;
        t.classList.toggle('selected', isSelected);
        t.querySelector('input').checked = isSelected;
    });

    if (value === 'perlu_tindakan') {
        rusakSection.style.display = 'block';
        notesInput.required = true;
        photoAfterInput.required = true;
    } else {
        rusakSection.style.display = 'none';
        notesInput.required = false;
        photoAfterInput.required = false;
    }
}
statusTaps.forEach(tap => tap.addEventListener('click', () => selectStatus(tap.dataset.status)));
selectStatus('aman');

// ── Photo Preview & AUTOMATIC COMPRESSION ──
async function setupPhotoCompressionAndPreview(inputId, areaId, placeholderId, previewId) {
    const input = document.getElementById(inputId);
    input.addEventListener('change', async function(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Tampilkan status loading di layar
        const placeholder = document.getElementById(placeholderId);
        const preview = document.getElementById(previewId);
        const originalHTML = placeholder.innerHTML;
        placeholder.innerHTML = '<div style="color:var(--p-primary); font-weight:700; font-size:0.8rem;">⏳ Mengompres foto...</div>';
        preview.style.display = 'none';
        placeholder.style.display = 'block';

        try {
            // Pengaturan: Maksimal 1MB, Resolusi 1280px (sangat cukup untuk bukti lapangan)
            const options = {
                maxSizeMB: 1,
                maxWidthOrHeight: 1280,
                useWebWorker: true
            };

            // Jalankan kompresi
            const compressedFile = await imageCompression(file, options);

            // Ganti file di dalam input form secara ajaib
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(new File([compressedFile], file.name, {
                type: compressedFile.type,
                lastModified: Date.now()
            }));
            input.files = dataTransfer.files;

            // Tampilkan foto hasil kompresi di preview
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
                placeholder.innerHTML = originalHTML; // Kembalikan ke text asli jika nanti diganti lagi
            };
            reader.readAsDataURL(compressedFile);

        } catch (error) {
            console.error('Kompresi gagal:', error);
            alert('Gagal memproses foto. Pastikan format foto benar.');
            placeholder.innerHTML = originalHTML;
        }
    });
}

setupPhotoCompressionAndPreview('photoBeforeInput', 'photoBeforeArea', 'photoBeforePlaceholder', 'photoBeforePreview');
setupPhotoCompressionAndPreview('photoAfterInput', 'photoAfterArea', 'photoAfterPlaceholder', 'photoAfterPreview');

// ── Submit Loading State ──
document.getElementById('pengecekanForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:17px;height:17px;animation:spin 0.8s linear infinite;"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg> Memproses...';
});
</script>
@endpush