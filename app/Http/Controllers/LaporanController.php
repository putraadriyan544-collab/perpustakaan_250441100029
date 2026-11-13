<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Tampilkan Laporan dan Rekapitulasi Perpustakaan
     */
    public function index(): View
    {
        $kategoriRekap = Kategori::withCount('bukus')->get();

        $bukuPopuler = Buku::with('kategori')
            ->withCount('peminjamans')
            ->orderByDesc('peminjamans_count')
            ->take(10)
            ->get();

        $statusCount = [
            'total' => Peminjaman::count(),
            'menunggu' => Peminjaman::where('status', 'menunggu')->count(),
            'disetujui' => Peminjaman::where('status', 'disetujui')->count(),
            'dikembalikan' => Peminjaman::where('status', 'dikembalikan')->count(),
            'ditolak' => Peminjaman::where('status', 'ditolak')->count(),
        ];

        return view('laporan.index', compact('kategoriRekap', 'bukuPopuler', 'statusCount'));
    }
}

