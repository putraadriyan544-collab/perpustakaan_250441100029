@extends('layouts.app')

@section('title', 'Laporan Perpustakaan')

@section('content')
<div>
    <h2 class="judul-halaman">Laporan & Rekapitulasi Perpustakaan</h2>
    <p class="subjudul">Statistik sirkulasi peminjaman, persebaran kategori, dan buku terpopuler di perpustakaan digital.</p>

    <!-- Ringkasan Statistik -->
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-number">{{ $statusCount['total'] }}</div>
            <div class="stat-label">Total Riwayat Transaksi</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-number">{{ $statusCount['menunggu'] }}</div>
            <div class="stat-label">Menunggu Persetujuan</div>
        </div>
        <div class="stat-card admin">
            <div class="stat-number">{{ $statusCount['disetujui'] }}</div>
            <div class="stat-label">Sedang Dipinjam</div>
        </div>
        <div class="stat-card success">
            <div class="stat-number">{{ $statusCount['dikembalikan'] }}</div>
            <div class="stat-label">Buku Berhasil Kembali</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px;">
        <!-- Rekap Buku Per Kategori -->
        <div class="card">
            <div class="card-header">Rekap Jumlah Buku Per Kategori</div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Kategori</th>
                            <th style="text-align: right; width: 140px;">Jumlah Koleksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kategoriRekap as $kat)
                            <tr>
                                <td><strong>{{ $kat->nama }}</strong></td>
                                <td style="text-align: right;">
                                    <span class="badge">{{ $kat->bukus_count }} Judul</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" style="text-align: center; padding: 16px;">Belum ada kategori terdata.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 10 Buku Paling Sering Dipinjam -->
        <div class="card">
            <div class="card-header">Daftar Buku Paling Banyak Dipinjam</div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Judul Buku</th>
                            <th>Kategori</th>
                            <th style="text-align: right; width: 120px;">Total Dipinjam</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bukuPopuler as $b)
                            <tr>
                                <td><strong>{{ $b->judul }}</strong></td>
                                <td>{{ $b->kategori->nama ?? '-' }}</td>
                                <td style="text-align: right;">
                                    <span class="badge badge-success">{{ $b->peminjamans_count }} kali</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 16px;">Belum ada peminjaman buku.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

