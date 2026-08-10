<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

// ─── Admin Controllers ────────────────────────────────────────────────────────
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\KategoriBarangController;
use App\Http\Controllers\Admin\LokasiController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\LogAktivitasController;

// ─── User / Petugas Controllers ───────────────────────────────────────────────
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\User\JadwalController as UserJadwalController;
use App\Http\Controllers\User\ScanController;
use App\Http\Controllers\User\PengecekanController;
use App\Http\Controllers\User\AkunController;


// ═══════════════════════════════════════════════════════════════════════════
// AUTH
// ═══════════════════════════════════════════════════════════════════════════
Route::get('/',      [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login',  [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


// ═══════════════════════════════════════════════════════════════════════════
// ADMIN
// ═══════════════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Kelola User
    Route::post('/users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');
    Route::resource('users', UserController::class);

    // Kelola Barang
    Route::post('/barang/bulk-delete', [BarangController::class, 'bulkDelete'])->name('barang.bulk-delete');
    Route::resource('barang', BarangController::class);

    // Kategori Barang
    Route::resource('kategori', KategoriBarangController::class)->only(['index', 'store', 'update', 'destroy']);

    // Lokasi Barang
    Route::resource('lokasi', LokasiController::class)->only(['index', 'show', 'store', 'update', 'destroy']);

    // Manajemen Waktu
    Route::resource('waktu', \App\Http\Controllers\Admin\ManajemenWaktuController::class)->except(['create', 'show']);

    // Manajemen Jadwal
    Route::post('/jadwal/perpanjang-tahun-depan', [JadwalController::class, 'perpanjangTahunDepan'])->name('jadwal.perpanjang');
    Route::post('/jadwal/update-frekuensi',       [JadwalController::class, 'updateFrekuensi'])->name('jadwal.update-frekuensi');
    Route::post('/jadwal/{jadwal}/selesai',       [JadwalController::class, 'selesai'])->name('jadwal.selesai');
    Route::post('/jadwal/bulk-delete',            [JadwalController::class, 'bulkDelete'])->name('jadwal.bulk-delete');
    Route::get('/jadwal/kalender-detail',         [JadwalController::class, 'kalenderDetail'])->name('jadwal.kalender-detail');
    
    Route::get('/jadwal/get-delete-data',         [JadwalController::class, 'getJadwalForDelete'])->name('jadwal.get-delete-data');
    Route::post('/jadwal/bulk-delete-advanced',   [JadwalController::class, 'bulkDeleteAdvanced'])->name('jadwal.bulk-delete-advanced');

    Route::resource('jadwal', JadwalController::class);

    // Generate QR Code
    Route::get('/qrcode',                        [QrCodeController::class, 'index'])->name('qrcode.index');
    Route::post('/qrcode/generate-all',          [QrCodeController::class, 'generateAll'])->name('qrcode.generate-all');
    Route::post('/qrcode/{barang}/generate',     [QrCodeController::class, 'generate'])->name('qrcode.generate');
    Route::get('/qrcode/{qrcode}/download',      [QrCodeController::class, 'download'])->name('qrcode.download');
    Route::get('/qrcode/{qrcode}/cetak',         [QrCodeController::class, 'cetak'])->name('qrcode.cetak');
    Route::get('/qrcode/cetak-semua',            [QrCodeController::class, 'cetakSemua'])->name('qrcode.cetak-semua');

    // Monitoring & Review
    Route::get('/monitoring',                    [MonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/{pengecekan}',       [MonitoringController::class, 'show'])->name('monitoring.show');
    Route::get('/review',                         [ReviewController::class, 'index'])->name('review.index');
    Route::get('/review/{pengecekan}',            [ReviewController::class, 'show'])->name('review.show');
    Route::post('/review/{pengecekan}/tindak-lanjuti', [ReviewController::class, 'tindakLanjuti'])->name('review.tindak-lanjuti');
    Route::post('/review/{pengecekan}/abaikan',        [ReviewController::class, 'abaikan'])->name('review.abaikan');
    Route::post('/review/{pengecekan}/selesaikan',     [ReviewController::class, 'selesaikan'])->name('review.selesaikan');

    // Laporan
    Route::get('/laporan',       [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');
    Route::get('/laporan/pdf',   [LaporanController::class, 'exportPdf'])->name('laporan.pdf');
    Route::get('/laporan/csv',   [LaporanController::class, 'exportCsv'])->name('laporan.csv');
    Route::post('/laporan/wipe-data', [LaporanController::class, 'wipeDataByCategory'])->name('laporan.wipe-data');
    
    // Pengaturan & Log
    Route::get('/pengaturan',              [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::post('/pengaturan/profil',      [PengaturanController::class, 'updateProfil'])->name('pengaturan.profil');
    Route::post('/pengaturan/password',    [PengaturanController::class, 'updatePassword'])->name('pengaturan.password');
    Route::post('/pengaturan/notifikasi',  [PengaturanController::class, 'updateNotifikasi'])->name('pengaturan.notifikasi');
    Route::get('/log-aktivitas', [LogAktivitasController::class, 'index'])->name('log-aktivitas.index');
});

Route::get('/scan/{kode_barang}', [\App\Http\Controllers\ScanController::class, 'index'])->name('scan.index');
Route::get('/auditor/story/{kode_barang}', [\App\Http\Controllers\ScanController::class, 'auditorStory'])->name('auditor.story');

// ═══════════════════════════════════════════════════════════════════════════
// USER / PETUGAS
// ═══════════════════════════════════════════════════════════════════════════
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {

    Route::get('/dashboard', [UserDashboard::class, 'index'])->name('dashboard');

    Route::get('/jadwal',             [UserJadwalController::class, 'index'])->name('jadwal.index');
    Route::get('/jadwal/{jadwal}',  [UserJadwalController::class, 'show'])->name('jadwal.show');

     // Scan QR Code
    Route::get('/scan',                      [ScanController::class, 'index'])->name('scan');
    Route::get('/scan/validasi/{token}',     [ScanController::class, 'validasi'])->name('scan.validasi');
    Route::post('/scan/proses',              [ScanController::class, 'prosesPetugas'])->name('scan.proses');

    // Pengecekan
    Route::get('/pengecekan/{jadwal}/mulai',   [PengecekanController::class, 'mulai'])->name('pengecekan.mulai');
    Route::post('/pengecekan/{jadwal}/submit', [PengecekanController::class, 'submit'])->name('pengecekan.submit');
    Route::get('/pengecekan/{pengecekan}',     [PengecekanController::class, 'show'])->name('pengecekan.show');

    // Riwayat & Akun
    Route::get('/riwayat', [PengecekanController::class, 'riwayat'])->name('riwayat');
    Route::post('/notifikasi/{id}/baca',  [\App\Http\Controllers\User\NotifikasiController::class, 'tandaiBaca'])->name('notifikasi.baca');
    Route::post('/notifikasi/baca-semua', [\App\Http\Controllers\User\NotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca-semua');

    Route::get('/akun',                 [AkunController::class, 'index'])->name('akun.index');
    Route::post('/akun/profil',         [AkunController::class, 'updateProfil'])->name('akun.profil');
    Route::post('/akun/password',       [AkunController::class, 'updatePassword'])->name('akun.password');
});

// Scan Portal — AJAX login (tidak butuh auth dulu)
Route::post('/scan/login-ajax', [\App\Http\Controllers\ScanLoginController::class, 'login'])->name('scan.login.ajax');

// Helper DB Fix
Route::get('/fix-kategori-db', function() {
    try {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('kategori_barang', 'checklist')) {
            \Illuminate\Support\Facades\Schema::table('kategori_barang', function ($table) {
                $table->text('checklist')->nullable()->after('deskripsi');
            });
            return "<h1 style='color: green; font-family: sans-serif; text-align: center; margin-top: 20%;'>✅ LACI CHECKLIST BERHASIL DIBUAT!</h1>";
        }
        return "<h1 style='color: blue; font-family: sans-serif; text-align: center; margin-top: 20%;'>ℹ️ Laci checklist sudah ada.</h1>";
    } catch (\Exception $e) {
        return "Gagal: " . $e->getMessage();
    }
});