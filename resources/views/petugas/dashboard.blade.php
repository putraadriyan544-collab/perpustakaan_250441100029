@extends('layouts.app')

@section('title', 'Dashboard Petugas')

@section('content')
<div>
    <h2 class="judul-halaman">Dashboard Operasional Petugas</h2>
    <p class="subjudul">Selamat bertugas, <strong>{{ Auth::user()->name }}</strong>. Kelola sirkulasi buku dan layanan perpustakaan di bawah ini.</p>

    <!-- Statistik Utama Petugas -->
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-number">{{ $totalBuku }}</div>
            <div class="stat-label">Total Koleksi Buku</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-number">{{ $peminjamanMenunggu }}</div>
            <div class="stat-label">Menunggu Persetujuan</div>
        </div>
        <div class="stat-card admin">
            <div class="stat-number">{{ $peminjamanAktif }}</div>
            <div class="stat-label">Sedang Dipinjam</div>
        </div>
        <div class="stat-card success">
            <div class="stat-number">{{ $peminjamanSelesai }}</div>
            <div class="stat-label">Pengembalian Selesai</div>
        </div>
    </div>

    <!-- Tombol Navigasi Operasional -->
    <div class="card">
        <div class="card-header">Layanan Sirkulasi Perpustakaan</div>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('buku.index') }}" class="btn">Kelola Data Buku</a>
            <a href="{{ route('peminjaman.index') }}" class="btn">Kelola Peminjaman & Pengembalian</a>
            <a href="{{ route('laporan.index') }}" class="btn btn-secondary">Lihat Laporan Perpustakaan</a>
        </div>
    </div>

    <!-- Permohonan Peminjaman Menunggu Persetujuan -->
    <div class="card">
        <div class="card-header">Permohonan Peminjaman Buku Masuk</div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Siswa / Pemohon</th>
                        <th>Judul Buku</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th style="text-align: center; width: 200px;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarMenunggu as $p)
                        <tr>
                            <td><strong>{{ $p->user->name ?? ($p->anggota->nama ?? 'Pengguna') }}</strong></td>
                            <td>{{ $p->buku->judul ?? '-' }}</td>
                            <td>{{ $p->tanggal_pinjam }}</td>
                            <td><span class="badge badge-warning">Menunggu Persetujuan</span></td>
                            <td style="text-align: center;">
                                <form method="POST" action="{{ route('peminjaman.approve', $p->id) }}" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-sm">Setujui</button>
                                </form>
                                <form method="POST" action="{{ route('peminjaman.reject', $p->id) }}" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-danger btn-sm">Tolak</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 20px;">Tidak ada permohonan peminjaman yang menunggu saat ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

