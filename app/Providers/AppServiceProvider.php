<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\Pengecekan;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // ── View Composer untuk layout Admin ──────────────
        // Otomatis tersedia di semua halaman Admin tanpa ubah tiap controller
        View::composer('layouts.admin', function ($view) {
            if (!Auth::check() || Auth::user()->role !== 'admin') return;

            // Badge "Review Hasil" di sidebar (pengecekan menunggu review)
            $menungguReviewCount = Pengecekan::where('status', 'perlu_tindakan')
                ->where('status_tindak_lanjut', 'menunggu')
                ->whereNotNull('jadwal_id')
                ->count();

            // Notifikasi bell — baca dari tabel notifications milik admin ini
            $notifList = Notification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->latest()
                ->take(5)
                ->get();

            $notifikasiCount = $notifList->count();
            
            $notifikasiList = $notifList->map(function ($n) {
                return [
                    'judul' => $n->judul,
                    'sub'   => \Illuminate\Support\Str::limit($n->pesan, 60),
                    'warna' => $n->tipe === 'perlu_tindakan' ? '#ef4444' : '#2563eb',
                    'waktu' => $n->created_at,
                    'url'   => $n->link && $n->link_id
                                ? route($n->link, $n->link_id)
                                : route('admin.monitoring.index'),
                ];
            });

            $view->with([
                'menungguReviewCount' => $menungguReviewCount,
                'notifikasiCount'     => $notifikasiCount,
                'notifikasiList'      => $notifikasiList,
            ]);
        });

        // ── View Composer untuk layout User ───────────────
        // Badge notifikasi untuk petugas (jadwal baru sesuai spesialisasi)
        View::composer('layouts.user', function ($view) {
            if (!Auth::check() || Auth::user()->role !== 'user') return;

            $user = Auth::user();

            // Ambil notifikasi yang belum dibaca milik user ini
            $notifList = Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->latest()
                ->take(10)
                ->get();

            $notifBadge = $notifList->count();

            // Semua notifikasi (termasuk sudah dibaca) untuk panel
            $notifAll = Notification::where('user_id', $user->id)
                ->latest()
                ->take(15)
                ->get();

            $view->with([
                'notifBadge' => $notifBadge,
                'notifList'  => $notifAll,
            ]);
        });
    }
}