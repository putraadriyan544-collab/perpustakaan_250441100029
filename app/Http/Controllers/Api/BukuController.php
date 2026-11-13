<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\BukuResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBukuRequest;
use App\Http\Requests\UpdateBukuRequest;
use App\Models\Kategori;
use App\Models\Buku;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    /**
     * 3a. Index => GET /api/buku
     * Mengembalikan daftar buku beserta relasi kategori (eager loading),
     * mendukung pencarian, filter, dan pagination.
     */
    public function index(Request $request)
    {
        $query = Buku::with('kategori')->withCount('peminjamans');

        // Filter pencarian berdasarkan judul atau penulis (Soal 4a)
        if ($request->has('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('judul', 'like', "%{$cari}%")
                  ->orWhere('penulis', 'like', "%{$cari}%");
            });
        }

        // Filter berdasarkan kategori_id (Soal 4b)
        if ($request->has('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        // Filter berdasarkan tahun terbit (Soal 4c)
        if ($request->has('tahun')) {
            $query->where('tahun_terbit', $request->tahun);
        }

        // Urutkan data terbaru lebih dulu (Soal 4 Ketentuan Tambahan a)
        $query->latest();

        // Pagination 10 data per halaman (Soal 4d & Ketentuan Tambahan c)
        $buku = $query->paginate(10)->withQueryString();

        return BukuResource::collection($buku);
    }

    /**
     * 3b. Store => POST /api/buku
     * Validasi via StoreBukuRequest & simpan dengan $request->validated().
     */
    public function store(StoreBukuRequest $request)
    {
        if (auth()->check() && !auth()->user()->can('Kelola data buku')) {
            return response()->json(['message' => 'Akses ditolak! Anda tidak memiliki izin kelola data buku.'], 403);
        }

        $buku = Buku::create($request->validated());

        return response()->json([
            'message' => 'Buku berhasil ditambahkan',
            'data'    => $buku
        ], 201);
    }

    /**
     * 3c. Show => GET /api/buku/{id}
     * Menampilkan detail satu buku beserta relasi kategori dan peminjaman.
     */
    public function show(string $id)
    {
        $buku = Buku::with(['kategori', 'peminjamans'])->find($id);

        if (!$buku) {
            return response()->json([
                'message' => 'not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail data buku',
            'data'    => $buku
        ], 200);
    }

    /**
     * 3d. Update => PUT /api/buku/{id}
     * Validasi via UpdateBukuRequest & update dengan $request->validated().
     */
    public function update(UpdateBukuRequest $request, string $id)
    {
        if (auth()->check() && !auth()->user()->can('Kelola data buku')) {
            return response()->json(['message' => 'Akses ditolak! Anda tidak memiliki izin kelola data buku.'], 403);
        }

        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json([
                'message' => 'not found'
            ], 404);
        }

        $buku->update($request->validated());

        return response()->json([
            'message' => 'Buku berhasil diperbarui',
            'data'    => $buku
        ], 200);
    }

    /**
     * 3e. Destroy => DELETE /api/buku/{id}
     * Menghapus data buku.
     */
    public function destroy(string $id)
    {
        if (auth()->check() && !auth()->user()->can('Kelola data buku')) {
            return response()->json(['message' => 'Akses ditolak! Anda tidak memiliki izin kelola data buku.'], 403);
        }

        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json([
                'message' => 'not found'
            ], 404);
        }

        $buku->delete();

        return response()->json([
            'message' => 'Buku berhasil dihapus'
        ], 200);
    }

    // 7a. GET /api/rekap/buku-per-kategori
    public function rekapBukuPerKategori()
    {
        $rekap = Kategori::withCount('bukus')->get();

        return response()->json([
            'message' => 'Rekap jumlah buku per kategori',
            'data'    => $rekap
        ], 200);
    }

    // 7b. GET /api/rekap/peminjaman-per-buku
    public function rekapPeminjamanPerBuku()
    {
        $rekap = Buku::withCount('peminjamans')->get();

        return response()->json([
            'message' => 'Rekap jumlah peminjaman per buku',
            'data'    => $rekap
        ], 200);
    }
}
