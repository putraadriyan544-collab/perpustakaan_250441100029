@extends('layouts.app')

@section('title', 'Riwayat Peminjaman Saya')

@section('content')
<div>
    <h2 class="judul-halaman">Riwayat & Status Peminjaman Saya</h2>
    <p class="subjudul">Pantau status pengajuan buku dan riwayat peminjaman buku perpustakaan Anda, <strong>{{ Auth::user()->name }}</strong>.</p>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div class="card-header" style="border: none; margin: 0; padding: 0;">Daftar Peminjaman Pribadi</div>
            <a href="{{ route('buku.index') }}" class="btn btn-sm">+ Ajukan Pinjam Buku Lain</a>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Judul Buku</th>
                        <th>Penulis</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                        <th style="text-align: center; width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($peminjamans as $index => $pinjam)
                        <tr>
                            <td>{{ $peminjamans->firstItem() + $index }}</td>
                            <td><strong>{{ $pinjam->buku->judul ?? 'Buku Tidak Diketahui' }}</strong></td>
                            <td>{{ $pinjam->buku->penulis ?? '-' }}</td>
                            <td>{{ $pinjam->tanggal_pinjam }}</td>
                            <td>{{ $pinjam->tanggal_kembali ?? '-' }}</td>
                            <td>
                                @if ($pinjam->status === 'disetujui')
                                    <span class="badge badge-success">Sedang Dipinjam</span>
                                @elseif ($pinjam->status === 'menunggu')
                                    <span class="badge badge-warning">Menunggu Persetujuan</span>
                                @elseif ($pinjam->status === 'dikembalikan')
                                    <span class="badge badge-info">Sudah Dikembalikan</span>
                                @else
                                    <span class="badge badge-danger">Pengajuan Ditolak</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('siswa.peminjaman.show', $pinjam->id) }}" class="btn btn-outline btn-sm">Lihat Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 30px;">
                                <p style="color: #777; margin-bottom: 12px;">Anda belum memiliki riwayat peminjaman buku.</p>
                                <a href="{{ route('buku.index') }}" class="btn btn-sm">Jelajahi Koleksi Buku Sekarang</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            {{ $peminjamans->links() }}
        </div>
    </div>
</div>
@endsection

