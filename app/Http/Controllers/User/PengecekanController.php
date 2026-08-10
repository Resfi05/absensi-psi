<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Pengecekan;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class PengecekanController extends Controller
{
    public function mulai(Request $request, Jadwal $jadwal)
    {
        $user = Auth::user();
        $jadwal->load(['barang.kategori', 'barang.lokasiRelasi']);

        // 🔥 SATPAM 2: CEK STATUS BARANG AKTIF SAAT MEMBUKA FORM
        if (!$jadwal->barang->is_active) {
            abort(403, 'Akses Ditolak: Barang ini sudah dinonaktifkan oleh Admin.');
        }

        // 🔥 REVISI BUG 403: Cek Hak Akses (Many-to-Many Spesialisasi)
        $spesialisasiIds = $user->spesialisasi->pluck('id')->toArray();
        if ($jadwal->user_id !== $user->id && !in_array($jadwal->barang->kategori_id, $spesialisasiIds)) {
            abort(403, 'Akses Ditolak: Tugas ini bukan spesialisasi Anda dan Anda tidak ditugaskan oleh Admin.');
        }

        // ====== GATE WAJIB: harus punya tiket scan QR yang valid ======
        $ticketData = session("scan_ticket_{$jadwal->id}");
        $ticketFromUrl = $request->query('ticket');

        $valid = $ticketData
            && $ticketFromUrl
            && $ticketData['ticket'] === $ticketFromUrl
            && $ticketData['expires_at'] >= now()->timestamp;

        if (!$valid) {
            return redirect()
                ->route('user.scan', ['jadwal' => $jadwal->id])
                ->with('error', 'Sesi scan kedaluwarsa. Silakan scan QR Code barang lagi.');
        }

        // TARIK CHECKLIST DINAMIS DARI DATABASE KATEGORI
        $checklistString = $jadwal->barang->kategori->checklist ?? '';
        
        // Pecah string checklist berdasarkan enter (baris baru) menjadi array
        $checklistTemplate = array_filter(array_map('trim', explode("\n", $checklistString)));
        
        // Jika Admin belum mengisi checklist di kategori, berikan checklist default
        if (empty($checklistTemplate)) {
            $checklistTemplate = [
                'Kondisi fisik baik',
                'Berfungsi normal',
                'Tidak ada kerusakan terlihat'
            ];
        }

        session(['pengecekan_mulai_' . $jadwal->id => now()->toDateTimeString()]);

        // Jika jenis jadwal adalah perbaikan, arahkan ke view form perbaikan khusus
        if ($jadwal->jenis_jadwal === 'perbaikan' && $jadwal->parent_pengecekan_id) {
            $parentPengecekan = Pengecekan::find($jadwal->parent_pengecekan_id);
            return view('user.pengecekan.form-perbaikan', compact('jadwal', 'parentPengecekan'));
        }

        return view('user.pengecekan.form', compact('jadwal', 'checklistTemplate'));
    }

    public function submit(Request $request, Jadwal $jadwal)
    {
        $user = Auth::user();

        // 🔥 SATPAM 3: CEK STATUS BARANG AKTIF SAAT MENEKAN TOMBOL SUBMIT
        if (!$jadwal->barang->is_active) {
            abort(403, 'Akses Ditolak: Barang ini sudah dinonaktifkan oleh Admin.');
        }

        // 🔥 REVISI BUG 403: Cek Hak Akses (Many-to-Many Spesialisasi) saat submit
        $spesialisasiIds = $user->spesialisasi->pluck('id')->toArray();
        if ($jadwal->user_id !== $user->id && !in_array($jadwal->barang->kategori_id, $spesialisasiIds)) {
            abort(403, 'Akses Ditolak: Tugas ini bukan spesialisasi Anda dan Anda tidak ditugaskan oleh Admin.');
        }

        if (empty($request->all()) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
            return redirect()->back()->withInput()->with('error', 'Gagal: Ukuran foto dari kamera HP terlalu besar. Coba turunkan resolusi kamera HP Anda.');
        }

        try {
            if ($jadwal->jenis_jadwal === 'perbaikan') {
                $rules = [
                    'photo_after' => 'required|image|mimes:jpg,jpeg,png,webp|max:51200', 
                    'notes'       => 'nullable|string|max:1000',
                ];
                $validated = $request->validate($rules);
                $validated['status'] = 'aman'; // Otomatis aman setelah diperbaiki
            } else {
                // Aturan standar untuk pengecekan rutin biasa
                $rules = [
                    'status'        => 'required|in:aman,perlu_tindakan',
                    'checklist'     => 'required|array',
                    'photo_before'  => 'required|image|mimes:jpg,jpeg,png,webp|max:51200',
                ];

                if ($request->status === 'perlu_tindakan') {
                    $rules['notes']       = 'required|string|max:1000';
                    $rules['photo_after'] = 'required|image|mimes:jpg,jpeg,png,webp|max:51200'; 
                } else {
                    $rules['notes']       = 'nullable|string|max:1000';
                    $rules['photo_after'] = 'nullable|image|mimes:jpg,jpeg,png,webp|max:51200';
                }
                $validated = $request->validate($rules);
            }

        } catch (ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();
            return redirect()->back()->withInput()->with('error', 'Gagal Submit: ' . $firstError);
        }

        // Proses checklist data
        $checklistData = [];
        if (isset($validated['checklist'])) {
            foreach ($validated['checklist'] as $item => $checked) {
                $checklistData[] = [
                    'item'    => $item,
                    'checked' => $checked === '1' || $checked === true,
                ];
            }
        } else {
            $checklistData = [['item' => 'Perbaikan Menyeluruh', 'checked' => true]];
        }

        // Upload File Foto
        $photoBeforePath = $request->hasFile('photo_before') 
            ? $request->file('photo_before')->store('pengecekan', 'public')
            : null;
            
        $photoAfterPath  = $request->hasFile('photo_after')
            ? $request->file('photo_after')->store('pengecekan', 'public')
            : null;

        $statusTindakLanjut = $validated['status'] === 'perlu_tindakan' ? 'menunggu' : null;

        $pengecekan = Pengecekan::create([
            'jadwal_id'            => $jadwal->id,
            'user_id'              => $user->id,
            'checklist_data'       => $checklistData,
            'photo_before'         => $photoBeforePath,
            'photo_after'          => $photoAfterPath,
            'notes'                => $validated['notes'] ?? null,
            'status'               => $validated['status'],
            'status_tindak_lanjut' => $statusTindakLanjut,
            'checked_at'           => now(),
        ]);

        // OTOMATISASI CLEAR TINDAK LANJUT
        if ($jadwal->jenis_jadwal === 'perbaikan' && $jadwal->parent_pengecekan_id) {
            $parent = Pengecekan::find($jadwal->parent_pengecekan_id);
            if ($parent) {
                $parent->update(['status_tindak_lanjut' => 'selesai']);
            }
        }

        // Catat log
        $statusLabel = $jadwal->jenis_jadwal === 'perbaikan' ? 'Selesai Perbaikan' : ($validated['status'] === 'aman' ? 'Aman' : 'Perlu Tindakan');
        ActivityLog::create([
            'user_id'    => $user->id,
            'aksi'       => 'pengecekan',
            'model_type' => Pengecekan::class,
            'model_id'   => $pengecekan->id,
            'deskripsi'  => "{$user->name} menyelesaikan " . ($jadwal->jenis_jadwal === 'perbaikan' ? 'perbaikan' : 'pengecekan') . " " .
                            "{$jadwal->barang->nama_barang} di " . ($jadwal->barang->lokasiRelasi->nama ?? '???') . " — Status: {$statusLabel}",
            'data_lama'  => null,
            'data_baru'  => ['status' => $validated['status'], 'notes' => $validated['notes'] ?? null],
            'ip_address' => request()->ip(),
        ]);

        // Notifikasi Admin
        $adminList = \App\Models\User::where('role', 'admin')->pluck('id');
        foreach ($adminList as $adminId) {
            \App\Models\Notification::kirim(
                userId: $adminId,
                judul:  $user->name . ($jadwal->jenis_jadwal === 'perbaikan' ? ' menyelesaikan perbaikan' : ' menyelesaikan pengecekan'),
                pesan:  "{$user->name} baru saja menyelesaikan " . ($jadwal->jenis_jadwal === 'perbaikan' ? 'perbaikan' : 'pengecekan') . " " .
                        "{$jadwal->barang->nama_barang} di " . ($jadwal->barang->lokasiRelasi->nama ?? '???') . " — Status: {$statusLabel}",
                tipe:   $validated['status'] === 'perlu_tindakan' ? 'perlu_tindakan' : 'info',
                link:   'admin.monitoring.show',
                linkId: $pengecekan->id,
            );
        }

        $jadwal->update(['status' => 'selesai']);

        if ($jadwal->jenis_jadwal !== 'perbaikan') {
            $jadwal->buatJadwalBerikutnya();
        }

        session()->forget('pengecekan_mulai_' . $jadwal->id);
        session()->forget('scan_ticket_' . $jadwal->id);

        return redirect()
            ->route('user.pengecekan.show', $pengecekan->id)
            ->with('success', $jadwal->jenis_jadwal === 'perbaikan' ? 'Tugas perbaikan berhasil disubmit!' : 'Pengecekan berhasil disimpan!');
    }

    public function show(Pengecekan $pengecekan)
    {
        $user = Auth::user();
        if ($pengecekan->user_id !== $user->id) abort(403);
        $pengecekan->load(['jadwal.barang.kategori', 'jadwal.barang.lokasiRelasi']);
        return view('user.pengecekan.hasil', compact('pengecekan'));
    }

    public function riwayat(Request $request)
    {
        $user = Auth::user();
        $query = Pengecekan::with(['jadwal.barang.kategori', 'jadwal.barang.lokasiRelasi'])
            ->where('user_id', $user->id);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('jadwal.barang', fn($q) => $q
                ->where('nama_barang', 'like', "%{$s}%")
                ->orWhere('kode_barang', 'like', "%{$s}%"));
        }

        $riwayat = $query->latest('checked_at')->paginate(10)->withQueryString();
        return view('user.pengecekan.riwayat', compact('riwayat'));
    }
}