<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\View\View;

class PetugasController extends Controller
{
    /**
     * Tampilkan Dashboard Petugas
     */
    public function dashboard(): View
    {
        $totalBuku = Buku::count();
        $peminjamanMenunggu = Peminjaman::where('status', 'menunggu')->count();
        $peminjamanAktif = Peminjaman::where('status', 'disetujui')->count();
        $peminjamanSelesai = Peminjaman::where('status', 'dikembalikan')->count();

        $daftarMenunggu = Peminjaman::with(['user', 'buku'])
            ->where('status', 'menunggu')
            ->latest()
            ->take(8)
            ->get();

        return view('petugas.dashboard', compact(
            'totalBuku',
            'peminjamanMenunggu',
            'peminjamanAktif',
            'peminjamanSelesai',
            'daftarMenunggu'
        ));
    }
}

