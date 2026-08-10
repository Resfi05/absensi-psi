<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog; // Jika kamu menggunakan Spatie, modelnya mungkin Spatie\Activitylog\Models\Activity
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        // Filter jenis aksi (meng-handle variasi nama aksi: created vs create)
        if ($request->filled('aksi') && $request->aksi !== 'semua') {
            $aksi = $request->aksi;
            if ($aksi === 'created') {
                $query->whereIn('aksi', ['created', 'create']);
            } elseif ($aksi === 'updated') {
                $query->whereIn('aksi', ['updated', 'update']);
            } elseif ($aksi === 'deleted') {
                $query->whereIn('aksi', ['deleted', 'delete']);
            } else {
                $query->where('aksi', $aksi);
            }
        }

        // Filter user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter Rentang Waktu (Hari ini, Minggu ini, Bulan ini)
        if ($request->filled('rentang_waktu') && $request->rentang_waktu !== 'semua') {
            if ($request->rentang_waktu === 'hari_ini') {
                $query->whereDate('created_at', Carbon::today());
            } elseif ($request->rentang_waktu === 'minggu_ini') {
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            } elseif ($request->rentang_waktu === 'bulan_ini') {
                $query->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
            }
        } else {
            $query->where('created_at', '>=', now()->subDays(30));
        }

        // Search
        if ($request->filled('search')) {
            $query->where('deskripsi', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->get('per_page', 15);
        $logs    = $query->latest()->paginate($perPage)->withQueryString();

        // Stats ringkas
        $totalHariIni = ActivityLog::whereDate('created_at', today())->count();
        $totalMingguIni = ActivityLog::where('created_at', '>=', now()->startOfWeek())->count();
        $totalBulanIni = ActivityLog::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

        $userList = User::orderBy('name')->get();

        return view('admin.log-aktivitas.index', compact(
            'logs', 'totalHariIni', 'totalMingguIni', 'totalBulanIni', 'userList'
        ));
    }
}