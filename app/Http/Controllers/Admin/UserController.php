<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\KategoriBarang;
use App\Models\Pengecekan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::select('users.*')->with('spesialisasi')
            ->addSelect(['pengecekan_count' => Pengecekan::selectRaw('count(*)')
                ->whereColumn('user_id', 'users.id')
            ]);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('username', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        if ($request->filled('role') && $request->role !== 'semua') {
            $query->where('role', $request->role);
        }

        // 🔥 REVISI: Filter spesialisasi (karena relasi berubah)
        if ($request->filled('spesialisasi')) {
            $query->whereHas('spesialisasi', function($q) use ($request) {
                $q->where('kategori_barang_id', $request->spesialisasi);
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $users      = $query->orderBy('name')->paginate(10)->withQueryString();
        $kategori_list = KategoriBarang::where('is_active', true)->orderBy('nama')->get();

        return view('admin.users.index', compact('users', 'kategori_list'));
    }

    public function create()
    {
        $kategori_list = KategoriBarang::where('is_active', true)->orderBy('nama')->get();
        return view('admin.users.create', compact('kategori_list'));
    }

    public function store(Request $request)
    {
        // 🔥 REVISI POIN 2: Validasi Ketat No HP & Email
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'username'        => 'required|string|max:50|unique:users,username',
            'email'           => 'required|email:rfc,dns|unique:users,email',
            'password'        => 'required|string|min:6|confirmed',
            'role'            => 'required|in:admin,user',
            'spesialisasi_id' => 'nullable|array', // 🔥 Berubah jadi array karena checkbox
            'spesialisasi_id.*'=> 'exists:kategori_barang,id',
            'no_hp'           => ['nullable', 'string', 'regex:/^(08|\+62)[0-9]{8,13}$/'], // 🔥 Wajib 08/+62 & panjang 10-15 digit
        ], [
            'no_hp.regex' => 'Format No HP tidak valid. Harus diawali 08 atau +62 (10-15 digit).',
            'email.email' => 'Format email tidak valid (harus domain yang benar).'
        ]);

        $validated['password']  = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');

        // Buat user
        $user = User::create($validated);

        // 🔥 REVISI POIN 9: Simpan banyak spesialisasi
        if ($validated['role'] === 'user' && !empty($validated['spesialisasi_id'])) {
            $user->spesialisasi()->attach($validated['spesialisasi_id']);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User {$validated['name']} berhasil ditambahkan.");
    }

    public function edit(User $user)
    {
        $kategori_list = KategoriBarang::where('is_active', true)->orderBy('nama')->get();
        return view('admin.users.edit', compact('user', 'kategori_list'));
    }

    public function update(Request $request, User $user)
    {
        // 🔥 REVISI POIN 2: Validasi Ketat
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'username'        => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'email'           => ['required', 'email:rfc,dns', Rule::unique('users', 'email')->ignore($user->id)],
            'password'        => 'nullable|string|min:6|confirmed',
            'role'            => 'required|in:admin,user',
            'spesialisasi_id' => 'nullable|array',
            'spesialisasi_id.*'=> 'exists:kategori_barang,id',
            'no_hp'           => ['nullable', 'string', 'regex:/^(08|\+62)[0-9]{8,13}$/'],
        ], [
            'no_hp.regex' => 'Format No HP tidak valid. Harus diawali 08 atau +62 (10-15 digit).'
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active');
        $user->update($validated);

        // 🔥 REVISI POIN 9: Update (Sync) banyak spesialisasi
        if ($validated['role'] === 'user' && !empty($validated['spesialisasi_id'])) {
            $user->spesialisasi()->sync($validated['spesialisasi_id']);
        } else {
            // Jika admin, atau user tidak punya spesialisasi, bersihkan relasinya
            $user->spesialisasi()->detach();
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Tidak bisa menghapus akun yang sedang digunakan.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User {$name} berhasil dihapus.");
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        $currentUserId = auth()->id();
        $usersToDelete = User::whereIn('id', $request->ids)
            ->where('id', '!=', $currentUserId)
            ->get();

        $count = 0;
        foreach ($usersToDelete as $u) {
            $punyaRiwayat = Pengecekan::where('user_id', $u->id)->exists();
            if (!$punyaRiwayat) {
                $u->delete();
                $count++;
            }
        }

        $skipped = count($request->ids) - $count;
        $msg = "{$count} data user berhasil dihapus.";
        
        if ($skipped > 0) {
            $msg .= " ({$skipped} user dilewati karena sedang login atau sudah memiliki riwayat kerja).";
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', $msg);
    }
}