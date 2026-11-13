<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\ProfilController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Perpustakaan Digital (Modul 5 RBAC)
|--------------------------------------------------------------------------
*/

// Halaman Publik (Dapat diakses tanpa login)
Route::view('/', 'beranda')->name('beranda');
Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
Route::get('/buku/{id}', [BukuController::class, 'show'])->name('buku.show')->whereNumber('id');

// Route Autentikasi Laravel Breeze (Login, Register, Logout)
require __DIR__.'/auth.php';

// Route yang Memerlukan Login (Auth Middleware)
Route::middleware('auth')->group(function () {
    // celah A2
    Route::get('/buku-cari', function (\Illuminate\Http\Request $request) {
    $judul = $request->query('judul');
    // RENTAN: variabel $judul digabungkan langsung via kutip string
    $data = \Illuminate\Support\Facades\DB::select("SELECT * FROM bukus WHERE judul = ?", [$judul]);// $data = \Illuminate\Support\Facades\DB::select("SELECT * FROM bukus WHERE judul = '$judul'");
    return response()->json($data);
    }); 
    // BATAS CELAH A2
    // Celah A3 (CSRF): Menghapus buku via method GET
    
    // BATAS CELAH A3
    // Celah A4 (IDOR): Melihat peminjaman tanpa cek kepemilikan
    Route::get('/peminjaman/{id}', [PeminjamanController::class, 'showPublicDetail'])->name('peminjaman.show.public');
    // Central Dashboard Redirector sesuai Role
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->hasRole('petugas')) {
            return redirect()->route('petugas.dashboard');
        }
        return redirect()->route('siswa.peminjaman.index');
    })->name('dashboard');

    // Profil Pengguna (Lihat dan Ubah Profil Sendiri - Permission 15 & 16)
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::patch('/profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::delete('/profil', [ProfilController::class, 'destroy'])->name('profil.destroy');

    // Kompatibilitas Route Profil Breeze
    Route::get('/profile', [ProfilController::class, 'index'])->name('profile.edit');
    Route::patch('/profile', [ProfilController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfilController::class, 'destroy'])->name('profile.destroy');

    // Route Khusus Siswa (Role: Siswa)
    Route::middleware('role:siswa')->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/peminjaman', [PeminjamanController::class, 'siswaIndex'])->name('peminjaman.index');
        Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
        Route::get('/peminjaman/{peminjaman}', [PeminjamanController::class, 'siswaShow'])->name('peminjaman.show');
    });

    // Route Operasional Buku (Khusus yang memiliki Permission 'Kelola data buku' -> Admin & Petugas)
    // Route::middleware('permission:Kelola data buku')->group(function () {
    //     Route::post('/buku', [BukuController::class, 'store'])->name('buku.store');
    //     Route::put('/buku/{id}', [BukuController::class, 'update'])->name('buku.update');
    //     Route::delete('/buku/{id}', [BukuController::class, 'destroy'])->name('buku.destroy');
    // });

    //soal nomer 3
        // Proteksi Gate: Hanya Admin dan Petugas yang bisa mengelola buku
    Route::middleware('can:kelola-buku')->group(function () {
        Route::get('/buku/create', [BukuController::class, 'create'])->name('buku.create');
        Route::post('/buku', [BukuController::class, 'store'])->name('buku.store');
        Route::put('/buku/{id}', [BukuController::class, 'update'])->name('buku.update');
        Route::delete('/buku/{id}', [BukuController::class, 'destroy'])->name('buku.destroy');
    });

    // Route Peminjaman & Pengembalian (Khusus Admin & Petugas - Permission 7, 8, 10, 11)
    Route::middleware('permission:Kelola peminjaman')->prefix('peminjaman')->name('peminjaman.')->group(function () {
        Route::get('/', [PeminjamanController::class, 'index'])->name('index');
        Route::patch('/{peminjaman}/approve', [PeminjamanController::class, 'approve'])->name('approve');
        Route::patch('/{peminjaman}/reject', [PeminjamanController::class, 'reject'])->name('reject');
        Route::patch('/{peminjaman}/return', [PeminjamanController::class, 'returnBook'])->name('return');
    });

    // Route Laporan Perpustakaan (Khusus Admin & Petugas - Permission 14)
    Route::middleware('permission:Lihat laporan perpustakaan')->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    });

    // Route Dashboard Petugas (Khusus Petugas - Permission 2 / Role: Petugas)
    Route::middleware('role:petugas')->prefix('petugas')->name('petugas.')->group(function () {
        Route::get('/dashboard', [PetugasController::class, 'dashboard'])->name('dashboard');
    });

    // Route Khusus Admin (Dashboard, Kelola Pengguna & Penetapan Role - Role: Admin)
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::patch('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('users.updateRole');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    });
});
