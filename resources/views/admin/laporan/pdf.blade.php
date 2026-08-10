<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengecekan Barang</title>
    <style>
        /* Pengaturan Kertas & Font */
        @page { margin: 25px 30px; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 8pt; color: #1e293b; margin: 0; padding: 0; }
        
        /* Header Dokumen */
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1e40af; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 15pt; color: #1e40af; font-weight: bold; letter-spacing: 1px; }
        .header h3 { margin: 4px 0; font-size: 11pt; color: #0f172a; text-transform: uppercase; }
        .header p { margin: 0; font-size: 8pt; color: #64748b; }

        /* Box Statistik */
        .stats-table { width: 100%; margin-bottom: 15px; border-collapse: separate; border-spacing: 10px 0; }
        .stat-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; text-align: center; padding: 10px 5px; width: 25%; }
        .stat-val { font-size: 15pt; font-weight: bold; margin-bottom: 3px; }
        .stat-label { font-size: 7.5pt; color: #475569; font-weight: bold; text-transform: uppercase; }

        /* Tabel Utama */
        table.data { width: 100%; border-collapse: collapse; font-size: 8pt; table-layout: fixed; margin-bottom: 15px; }
        table.data th { background-color: #1e40af; color: #ffffff; text-align: center; padding: 8px 4px; font-size: 8pt; border: 1px solid #1e3a8a; text-transform: uppercase; }
        table.data td { padding: 6px 4px; border: 1px solid #cbd5e1; vertical-align: middle; word-wrap: break-word; }
        table.data tr:nth-child(even) { background-color: #f8fafc; }
        
        /* Badges & Teks */
        .badge { padding: 4px 6px; border-radius: 4px; font-weight: bold; font-size: 7pt; text-align: center; display: block; width: 85%; margin: 0 auto; }
        .badge-green { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
        .badge-red { background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; }
        .badge-purple { background: #ede9fe; color: #7c3aed; border: 1px solid #ddd6fe; }
        .badge-orange { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
        
        .text-center { text-align: center; }
        .text-muted { color: #64748b; }
        
        /* Pengaturan Gambar agar Tidak Pecah/Meluber */
        .foto-container { display: flex; justify-content: center; align-items: center; gap: 4px; }
        .img-thumbnail { width: 38px; height: 38px; border: 1px solid #cbd5e1; border-radius: 4px; background: #fff; }
        .tl-date { font-size: 6.5pt; color: #64748b; margin-top: 3px; display: block; }
    </style>
</head>
<body>

    <div class="header">
        <h2>PT PADMA SOODE INDONESIA</h2>
        <h3>LAPORAN HASIL PENGECEKAN BARANG & BUKTI FISIK</h3>
        <p>Periode: {{ $tglMulai->translatedFormat('d F Y') }} — {{ $tglAkhir->translatedFormat('d F Y') }}</p>
    </div>

    <table class="stats-table">
        <tr>
            <td class="stat-box">
                <div class="stat-val" style="color:#2563eb;">{{ $totalPengecekan }}</div>
                <div class="stat-label">Total Pengecekan</div>
            </td>
            <td class="stat-box">
                <div class="stat-val" style="color:#16a34a;">{{ $kondisiAman }}</div>
                <div class="stat-label">Kondisi Aman</div>
            </td>
            <td class="stat-box">
                <div class="stat-val" style="color:#ef4444;">{{ $prosesPerbaikan }}</div>
                <div class="stat-label">Proses Perbaikan</div>
            </td>
            <td class="stat-box">
                <div class="stat-val" style="color:#7c3aed;">{{ $selesaiDitutup }}</div>
                <div class="stat-label">Selesai / Ditutup</div>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 3.5%;">No</th>
                <th style="width: 9.5%;">Tanggal</th>
                <th style="width: 8%;">Kode</th>
                <th style="width: 14%;">Nama Barang</th>
                <th style="width: 14%;">Lokasi</th>
                <th style="width: 10%;">Petugas</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 9%;">Tindak Lanjut</th>
                <th style="width: 9%;">Foto Bukti</th>
                <th style="width: 13%;">Catatan / Temuan</th>
            </tr>
        </thead>
        <tbody>
            @php
                // ✨ RAHASIA: Fungsi Convert Gambar ke Base64 (Anti-Gagal di PDF)
                $imgToBase64 = function($path) {
                    if (!$path) return null;
                    if (str_starts_with($path, 'http')) return $path;
                    
                    $fullPath = public_path('storage/' . $path);
                    if (file_exists($fullPath)) {
                        $type = pathinfo($fullPath, PATHINFO_EXTENSION);
                        $data = file_get_contents($fullPath);
                        return 'data:image/' . $type . ';base64,' . base64_encode($data);
                    }
                    return null;
                };
            @endphp

            @forelse($data as $i => $row)
            @php
                $jadwal = $row['jadwal'];
                $p      = $row['pengecekan'];
                $barang = $jadwal->barang;
                $status = $row['status'];

                $statusBadge = match($status) {
                    'aman'             => 'badge-green',
                    'proses_perbaikan' => 'badge-red',
                    'selesai_ditutup'  => 'badge-purple',
                    default            => 'badge-orange',
                };
                $statusLabel = match($status) {
                    'aman'             => 'Aman',
                    'proses_perbaikan' => 'Perbaikan',
                    'selesai_ditutup'  => 'Selesai',
                    default            => 'Belum',
                };

                // Proses Gambar
                $fotoBef = $p ? $imgToBase64($p->photo_before) : null;
                $fotoAft = $p ? $imgToBase64($p->photo_after) : null;
            @endphp
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td class="text-center">
                    {{ $p && $p->checked_at ? \Carbon\Carbon::parse($p->checked_at)->format('d/m/Y H:i') : \Carbon\Carbon::parse($jadwal->tanggal_jadwal)->format('d/m/Y') }}
                </td>
                <td class="text-center"><strong>{{ $barang->kode_barang ?? '-' }}</strong></td>
                <td>{{ $barang->nama_barang ?? '-' }}</td>
                <td>{{ $barang->lokasiRelasi->nama ?? '-' }}</td>
                <td>{{ $p->user->name ?? $jadwal->user->name ?? '-' }}</td>
                
                <td class="text-center">
                    <span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span>
                </td>
                
                <td class="text-center">
                    @if($p && $p->status_tindak_lanjut)
                        <strong style="text-transform: uppercase; font-size: 7pt; color:#334155;">{{ $p->status_tindak_lanjut }}</strong>
                        @if(in_array($p->status_tindak_lanjut, ['selesai', 'ditangani', 'diabaikan']) && $p->updated_at)
                            <span class="tl-date">{{ \Carbon\Carbon::parse($p->updated_at)->format('d/m/y H:i') }}</span>
                        @endif
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                
                <td class="text-center">
                    @if($fotoBef || $fotoAft)
                        <div class="foto-container">
                            @if($fotoBef)
                                <img src="{{ $fotoBef }}" class="img-thumbnail" alt="Before">
                            @endif
                            @if($fotoAft)
                                <img src="{{ $fotoAft }}" class="img-thumbnail" alt="After">
                            @endif
                        </div>
                    @else
                        <span style="color:#94a3b8; font-size:7pt; font-style:italic;">Tanpa Foto</span>
                    @endif
                </td>

                <td style="font-size: 7.5pt;">
                    {{ $p && $p->notes ? \Illuminate\Support\Str::limit($p->notes, 50) : '-' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center" style="padding: 20px; color: #94a3b8; font-style:italic;">
                    Tidak ada data laporan yang ditemukan pada periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="font-size: 7.5pt; color: #94a3b8; text-align: right; margin-top: 15px;">
        <em>Dokumen ini dicetak secara otomatis oleh Sistem Inventaris & Pengecekan Barang pada {{ now()->translatedFormat('d F Y, H:i') }} WIB</em>
    </div>

</body>
</html>