@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div>
    <h2 class="judul-halaman">Dashboard Administrator</h2>
    <p class="subjudul">Selamat datang kembali, <strong>{{ Auth::user()->name }}</strong>. Berikut ringkasan operasional perpustakaan digital.</p>

    <!-- Statistik Utama -->
    <div class="stat-grid">
        <div class="stat-card admin">
            <div class="stat-number">{{ $totalBuku }}</div>
            <div class="stat-label">Total Koleksi Buku</div>
        </div>
        <div class="stat-card admin">
            <div class="stat-number">{{ $totalUser }}</div>
            <div class="stat-label">Total Pengguna Terdaftar</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-number">{{ $peminjamanMenunggu }}</div>
            <div class="stat-label">Permohonan Pinjam Baru</div>
        </div>
        <div class="stat-card success">
            <div class="stat-number">{{ $peminjamanAktif }}</div>
            <div class="stat-label">Buku Sedang Dipinjam</div>
        </div>
    </div>

    <!-- Tombol Navigasi Cepat -->
    <div class="card">
        <div class="card-header">Navigasi Cepat Administrasi</div>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('buku.index') }}" class="btn">Kelola Data Buku</a>
            <a href="{{ route('admin.users.index') }}" class="btn">Kelola Akun & Penetapan Role</a>
            <a href="{{ route('peminjaman.index') }}" class="btn">Proses Peminjaman & Pengembalian</a>
            <a href="{{ route('laporan.index') }}" class="btn btn-secondary">Lihat Laporan Perpustakaan</a>
        </div>
    </div>

    <!-- Peminjaman Terbaru -->
    <div class="card">
        <div class="card-header">Aktivitas Peminjaman Terbaru</div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Peminjam</th>
                        <th>Judul Buku</th>
                        <th>Tgl Pengajuan/Pinjam</th>
                        <th>Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($peminjamanTerbaru as $pinjam)
                        <tr>
                            <td><strong>{{ $pinjam->user->name ?? ($pinjam->anggota->nama ?? 'Pengguna') }}</strong></td>
                            <td>{{ $pinjam->buku->judul ?? '-' }}</td>
                            <td>{{ $pinjam->tanggal_pinjam }}</td>
                            <td>
                                @if ($pinjam->status === 'disetujui')
                                    <span class="badge badge-success">Dipinjam</span>
                                @elseif ($pinjam->status === 'menunggu')
                                    <span class="badge badge-warning">Menunggu Persetujuan</span>
                                @elseif ($pinjam->status === 'dikembalikan')
                                    <span class="badge badge-info">Dikembalikan</span>
                                @else
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('peminjaman.index') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 20px;">Belum ada aktivitas peminjaman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

