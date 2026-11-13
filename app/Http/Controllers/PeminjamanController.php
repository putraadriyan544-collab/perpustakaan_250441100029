<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    /**
     * Menampilkan semua data peminjaman (Khusus Admin & Petugas)
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = Peminjaman::with(['user', 'buku'])->latest();

        if ($status && in_array($status, ['menunggu', 'disetujui', 'dikembalikan', 'ditolak'])) {
            $query->where('status', $status);
        }

        $peminjamans = $query->paginate(15)->withQueryString();

        return view('peminjaman.index', compact('peminjamans', 'status'));
    }

    /**
     * Memproses Persetujuan Peminjaman (Admin & Petugas)
     */
    public function approve(Peminjaman $peminjaman): RedirectResponse
    {
        $peminjaman->update([
            'status' => 'disetujui',
            'tanggal_pinjam' => now()->toDateString(),
        ]);

        return back()->with('success', "Peminjaman buku '{$peminjaman->buku->judul}' berhasil disetujui.");
    }

    /**
     * Menolak Pengajuan Peminjaman (Admin & Petugas)
     */
    public function reject(Peminjaman $peminjaman): RedirectResponse
    {
        $peminjaman->update([
            'status' => 'ditolak',
        ]);

        return back()->with('success', "Pengajuan peminjaman buku '{$peminjaman->buku->judul}' telah ditolak.");
    }

    /**
     * Mencatat Pengembalian Buku (Admin & Petugas)
     */
    public function returnBook(Peminjaman $peminjaman): RedirectResponse
    {
        $peminjaman->update([
            'status' => 'dikembalikan',
            'tanggal_kembali' => now()->toDateString(),
        ]);

        return back()->with('success', "Pengembalian buku '{$peminjaman->buku->judul}' berhasil dicatat.");
    }

    /**
     * Siswa: Melihat status dan riwayat peminjaman milik sendiri
     */
    public function siswaIndex(): View
    {
        $user = Auth::user();

        // Hanya mengambil data milik siswa yang sedang login (memenuhi Soal 4 & Soal 5)
        $peminjamans = Peminjaman::with('buku')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('siswa.riwayat', compact('peminjamans'));
    }

    /**
     * Siswa: Mengajukan peminjaman buku
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'buku_id' => ['required', 'exists:bukus,id'],
        ]);

        $user = Auth::user();

        // Cek jika siswa masih memiliki pinjaman aktif / menunggu untuk buku ini
        $sedangPinjam = Peminjaman::where('user_id', $user->id)
            ->where('buku_id', $request->buku_id)
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->exists();

        if ($sedangPinjam) {
            return back()->with('error', 'Anda sudah mengajukan atau sedang meminjam buku ini.');
        }

        Peminjaman::create([
            'user_id' => $user->id,
            'buku_id' => $request->buku_id,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali' => null,
            'status' => 'menunggu',
        ]);

        return redirect()->route('siswa.peminjaman.index')->with('success', 'Pengajuan peminjaman buku berhasil dikirim. Menunggu persetujuan petugas.');
    }

    /**
     * Siswa: Melihat detail riwayat peminjaman
     * Pemeriksaan hak akses berdasarkan identitas pengguna login (Skenario 14)
     */
    public function siswaShow(Peminjaman $peminjaman): View
    {
        // Tolak jika bukan peminjaman miliknya sendiri
        if ($peminjaman->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak! Anda tidak diperkenankan melihat riwayat peminjaman pengguna lain.');
        }

        $peminjaman->load('buku.kategori');

        return view('siswa.detail-riwayat', compact('peminjaman'));
    }
        /**
     * Celah A4 (IDOR): Mengambil data peminjaman langsung tanpa memeriksa pemilik
     */
    public function showPublicDetail($id): View
    {
        $peminjaman = Peminjaman::with(['user', 'buku.kategori'])->findOrFail($id);
        return view('siswa.detail-riwayat', compact('peminjaman'));
    }
}

