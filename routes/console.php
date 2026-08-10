<?php

use Illuminate\Support\Facades\Schedule;

// ── Hapus notifikasi lama setiap hari tengah malam ──
// Notifikasi lebih dari 7 hari otomatis dihapus (semua, dibaca maupun belum)
Schedule::command('notifikasi:hapus-lama')->dailyAt('00:00');

// Jalankan script cek keterlambatan setiap malam jam 00:01 (pas ganti hari)
Schedule::command('perbaikan:cek-terlambat')->dailyAt('00:01');