<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    /**
     * Tampilkan Dashboard Admin
     */
    public function dashboard(): View
    {
        $totalBuku = Buku::count();
        $totalKategori = Kategori::count();
        $totalUser = User::count();
        $totalPeminjaman = Peminjaman::count();
        $peminjamanMenunggu = Peminjaman::where('status', 'menunggu')->count();
        $peminjamanAktif = Peminjaman::where('status', 'disetujui')->count();

        $peminjamanTerbaru = Peminjaman::with(['user', 'buku'])->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalBuku',
            'totalKategori',
            'totalUser',
            'totalPeminjaman',
            'peminjamanMenunggu',
            'peminjamanAktif',
            'peminjamanTerbaru'
        ));
    }

    /**
     * Kelola Data Pengguna dan Penetapan Role
     */
    public function users(): View
    {
        $users = User::with('roles')->latest()->get();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Tambah Pengguna Baru oleh Admin
     */
    public function storeUser(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Rules\Password::defaults()],
            'role' => ['required', 'string', Rule::in(['admin', 'petugas', 'siswa'])],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')->with('success', "Pengguna {$user->name} berhasil ditambahkan dengan role {$request->role}.");
    }

    /**
     * Ubah Role Pengguna oleh Admin
     */
    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'string', Rule::in(['admin', 'petugas', 'siswa'])],
        ]);

        // Mencegah admin mencabut role admin dari dirinya sendiri saat login
        if ($user->id === Auth::id() && $request->role !== 'admin') {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri saat sedang aktif.');
        }

        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')->with('success', "Role untuk pengguna {$user->name} berhasil diubah menjadi {$request->role}.");
    }

    /**
     * Hapus Pengguna oleh Admin
     */
    public function destroyUser(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $nama = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "Pengguna {$nama} berhasil dihapus.");
    }
}

