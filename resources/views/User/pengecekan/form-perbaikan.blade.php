@extends('layouts.user')

@section('title', 'Form Perbaikan Barang')

@section('content')
<div class="container-mobile" style="padding: 16px; max-width: 500px; margin: 0 auto; font-family: 'Inter', sans-serif;">
    
    {{-- HEADER TUGAS --}}
    <div style="background: linear-gradient(135deg, #ea580c, #f97316); border-radius: 16px; padding: 20px; color: white; box-shadow: 0 4px 15px rgba(234, 88, 12, 0.2); margin-bottom: 20px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
            <span style="background: rgba(255, 255, 255, 0.2); font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px;">
                Tugas Perbaikan
            </span>
        </div>
        <h2 style="margin: 0; font-size: 1.4rem; font-weight: 700; line-height: 1.3;">{{ $jadwal->barang->nama_barang }}</h2>
        <p style="margin: 4px 0 0 0; opacity: 0.9; font-size: 0.9rem; font-weight: 500;">Code: {{ $jadwal->barang->kode_barang }}</p>
        
        <div style="display: flex; align-items: center; gap: 6px; margin-top: 14px; font-size: 0.85rem; background: rgba(0,0,0,0.1); padding: 8px 12px; border-radius: 8px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; flex-shrink:0;">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
            </svg>
            <span>{{ $jadwal->barang->lokasiRelasi->nama ?? 'Lokasi tidak diketahui' }}</span>
        </div>
    </div>

    {{-- KOTAK INFORMASI KERUSAKAN SEBELUMNYA --}}
    <div style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 12px; padding: 16px; margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; color: #dc2626; margin-bottom: 8px; font-weight: 700; font-size: 0.9rem;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 18px; height: 18px;">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            Laporan Temuan Kerusakan
        </div>
        <p style="margin: 0; color: #4b5563; font-size: 0.9rem; line-height: 1.5; background: white; padding: 10px; border-radius: 6px; border: 1px solid #f3f4f6;">
            "{!! nl2br(e($parentPengecekan->notes ?? 'Tidak ada catatan khusus dari pemeriksaan rutin.')) !!}"
        </p>
        <div style="margin-top: 10px; font-size: 0.8rem; color: #9ca3af; display: flex; justify-content: space-between;">
            <span>Dilaporkan oleh: <strong>{{ $parentPengecekan->user->name ?? 'Petugas' }}</strong></span>
            <span>{{ \Carbon\Carbon::parse($parentPengecekan->checked_at)->translatedFormat('d M Y') }}</span>
        </div>
    </div>

    {{-- FORM INPUT --}}
    <form action="{{ route('user.pengecekan.submit', $jadwal->id) }}" method="POST" enctype="multipart/form-data" id="formPerbaikan" onsubmit="showLoading()">
        @csrf

        {{-- ALERT ERROR DARI VALIDASI CONTROLLER --}}
        @if(session('error'))
        <div style="background: #ef4444; color: white; padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 0.85rem; font-weight: 500; display: flex; align-items: center; gap: 8px;">
            <span style="cursor:pointer;" onclick="this.parentElement.remove()">✕</span>
            {{ session('error') }}
        </div>
        @endif

        {{-- UPLOAD FOTO BUKTI AFTER (WAJIB) --}}
        <div style="margin-bottom: 24px;">
            <label style="display: block; font-weight: 700; font-size: 0.95rem; color: #1f2937; margin-bottom: 6px;">
                Foto Bukti Setelah Perbaikan <span style="color: #dc2626;">*</span>
            </label>
            <p style="margin: 0 0 10px 0; font-size: 0.8rem; color: #6b7280;">Ambil foto kondisi barang yang sudah selesai diperbaiki dan siap digunakan kembali.</p>
            
            {{-- Box Preview & Input File --}}
            <div style="position: relative; border: 2px dashed #cbd5e1; border-radius: 12px; background: #f8fafc; overflow: hidden; min-height: 160px; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 16px; cursor: pointer;" onclick="document.getElementById('photo_after').click()">
                <input type="file" name="photo_after" id="photo_after" accept="image/*" capture="environment" style="display: none;" onchange="previewImage(this)">
                
                <div id="uploadPlaceholder" style="text-align: center; color: #64748b;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width: 40px; height: 40px; margin-bottom: 8px; color: #94a3b8;">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>
                    </svg>
                    <div style="font-weight: 600; font-size: 0.85rem;">Ketuk untuk Ambil Foto Kamera</div>
                    <div style="font-size: 0.75rem; opacity: 0.7; margin-top: 2px;">Format JPG, PNG, WEBP (Max 10MB)</div>
                </div>

                <img id="imagePreview" src="#" alt="Preview" style="display: none; width: 100%; max-height: 250px; object-fit: contain; border-radius: 6px;">
                
                <div id="btnUbahFoto" style="display: none; position: absolute; bottom: 8px; background: rgba(0,0,0,0.6); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 500;">
                    Ubah Foto
                </div>
            </div>
        </div>

        {{-- CATATAN PENGERJAAN / TINDAKAN --}}
        <div style="margin-bottom: 28px;">
            <label style="display: block; font-weight: 700; font-size: 0.95rem; color: #1f2937; margin-bottom: 6px;">
                Catatan Tindakan Perbaikan <span style="font-weight: 400; color: #9ca3af; font-size: 0.8rem;">(Opsional)</span>
            </label>
            <textarea name="notes" placeholder="Tuliskan tindakan yang dilakukan (misal: ganti pipa bocor, isi ulang freon, dll)..." style="width: 100%; min-height: 100px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; font-family: inherit; font-size: 0.9rem; color: #334155; resize: none; outline: none; box-sizing: border-box;" onfocus="this.style.borderColor='#ea580c'" onblur="this.style.borderColor='#cbd5e1'">{{ old('notes') }}</textarea>
        </div>

        {{-- BUTTON SUBMIT --}}
        <button type="submit" id="btnSubmit" style="width: 100%; background: #ea580c; color: white; border: none; border-radius: 10px; padding: 14px; font-size: 1rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3); transition: background 0.2s;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width: 18px; height: 18px;">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            Selesaikan Tugas & Tutup Laporan
        </button>

        {{-- NAVIGASI BATAL / KEMBALI --}}
        <a href="{{ route('user.jadwal.index') }}" style="display: flex; align-items: center; justify-content: center; width: 100%; margin-top: 12px; padding: 12px; color: #64748b; font-size: 0.9rem; font-weight: 600; text-decoration: none; border: 1px solid #e2e8f0; border-radius: 10px; background: white; box-sizing: border-box;">
            Batal & Kembali ke Jadwal
        </a>
    </form>
</div>

{{-- LOADING OVERLAY SCREEN (Mencegah Klik Ganda Saat Upload Foto Besar) --}}
<div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.9); z-index: 9999; flex-direction: column; align-items: center; justify-content: center; font-family: sans-serif;">
    <div style="width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #ea580c; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 12px;"></div>
    <div style="font-weight: 700; color: #1f2937; font-size: 1rem;">Sedang Menyimpan Data...</div>
    <div style="font-size: 0.8rem; color: #6b7280; margin-top: 4px;">Jangan tutup aplikasi atau mematikan HP.</div>
</div>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const placeholder = document.getElementById('uploadPlaceholder');
    const btnUbah = document.getElementById('btnUbahFoto');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Proses Kompresi Gambar Otomatis
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                // Buat kanvas virtual untuk mengecilkan ukuran
                const canvas = document.createElement('canvas');
                const MAX_WIDTH = 1200; // Maksimal lebar foto
                const MAX_HEIGHT = 1200; // Maksimal tinggi foto
                let width = img.width;
                let height = img.height;

                // Kalkulasi rasio
                if (width > height) {
                    if (width > MAX_WIDTH) {
                        height *= MAX_WIDTH / width;
                        width = MAX_WIDTH;
                    }
                } else {
                    if (height > MAX_HEIGHT) {
                        width *= MAX_HEIGHT / height;
                        height = MAX_HEIGHT;
                    }
                }

                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                // Ubah hasil kanvas menjadi file baru (kualitas 75%)
                canvas.toBlob((blob) => {
                    // Masukkan file yang sudah dikompres kembali ke input form
                    const dataTransfer = new DataTransfer();
                    const compressedFile = new File([blob], file.name, { type: "image/jpeg" });
                    dataTransfer.items.add(compressedFile);
                    input.files = dataTransfer.files;

                    // Tampilkan preview
                    preview.src = URL.createObjectURL(compressedFile);
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                    btnUbah.style.display = 'block';
                }, 'image/jpeg', 0.75);
            }
            img.src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
}

function showLoading() {
    document.getElementById('loadingOverlay').style.display = 'flex';
    document.getElementById('btnSubmit').disabled = true;
    document.getElementById('btnSubmit').style.background = '#9ca3af';
    document.getElementById('btnSubmit').style.boxShadow = 'none';
}
</script>
@endsection