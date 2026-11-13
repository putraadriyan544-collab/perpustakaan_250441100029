<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BukuController extends Controller
{
    /**
     * Tampilkan Halaman Daftar Buku (Semua pengguna / publik)
     */
    public function index(): View
    {
        $bukus = Buku::with('kategori')->latest()->get();
        return view('buku.index', compact('bukus'));
    }

    /**
     * Tampilkan Detail Buku
     */
    public function show(int $id): View
    {
        $buku = Buku::with('kategori')->findOrFail($id);
        return view('buku.show', compact('buku'));
    }

    /**
     * Halaman Pengelolaan Buku (Admin & Petugas)
     */
    public function kelola(): View
    {
        $bukus = Buku::with('kategori')->latest()->get();
        return view('buku.index', compact('bukus'));
    }
    /**
     * Form Tambah Buku
     */
    //soal nomer 2
    public function create(): View
    {
        $bukus = Buku::with('kategori')->latest()->get();
        return view('buku.index', compact('bukus'));
    }
    /**
     * Simpan Buku Baru via Form Web
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'tahun_terbit' => 'required|integer|digits:4',
        ]);

        $buku = Buku::create($validated);

        return redirect()->route('buku.index')->with('success', "Buku '{$buku->judul}' berhasil ditambahkan.");
    }

    /**
     * Perbarui Buku via Web
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $buku = Buku::findOrFail($id);

        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'tahun_terbit' => 'required|integer|digits:4',
        ]);

        $buku->update($validated);

        return redirect()->route('buku.index')->with('success', "Buku '{$buku->judul}' berhasil diperbarui.");
    }

    /**
     * Hapus Buku via Web
     */
    public function destroy(int $id): RedirectResponse
    {
        $buku = Buku::findOrFail($id);
        $judul = $buku->judul;
        $buku->delete();

        return redirect()->route('buku.index')->with('success', "Buku '{$judul}' berhasil dihapus.");
    }
}
