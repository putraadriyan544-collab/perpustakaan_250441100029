@extends('layouts.app')

@section('title', 'Kelola Peminjaman & Pengembalian')

@section('content')
<div>
    <h2 class="judul-halaman">Kelola Peminjaman & Pengembalian Buku</h2>
    <p class="subjudul">Pusat persetujuan peminjaman dan pencatatan sirkulasi pengembalian buku perpustakaan.</p>

    <!-- Filter Status -->
    <div style="display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap;">
        <a href="{{ route('peminjaman.index') }}" class="btn btn-sm {{ !$status ? 'btn' : 'btn-outline' }}">Semua Status</a>
        <a href="{{ route('peminjaman.index', ['status' => 'menunggu']) }}" class="btn btn-sm {{ $status === 'menunggu' ? 'btn-warning' : 'btn-outline' }}">Menunggu Persetujuan</a>
        <a href="{{ route('peminjaman.index', ['status' => 'disetujui']) }}" class="btn btn-sm {{ $status === 'disetujui' ? 'btn-success' : 'btn-outline' }}">Sedang Dipinjam</a>
        <a href="{{ route('peminjaman.index', ['status' => 'dikembalikan']) }}" class="btn btn-sm {{ $status === 'dikembalikan' ? 'btn' : 'btn-outline' }}">Sudah Dikembalikan</a>
        <a href="{{ route('peminjaman.index', ['status' => 'ditolak']) }}" class="btn btn-sm {{ $status === 'ditolak' ? 'btn-danger' : 'btn-outline' }}">Ditolak</a>
    </div>

    <div class="card">
        <div class="card-header">Daftar Transaksi Peminjaman</div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Peminjam</th>
                        <th>Judul Buku</th>
                        <th>Tgl Pinjam / Pengajuan</th>
                        <th>Tgl Kembali</th>
                        <th>Status</th>
                        <th style="text-align: center; width: 220px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($peminjamans as $index => $pinjam)
                        <tr>
                            <td>{{ $peminjamans->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $pinjam->user->name ?? ($pinjam->anggota->nama ?? 'Pengguna') }}</strong>
                                <br><small style="color: #777;">{{ $pinjam->user->email ?? ($pinjam->anggota->email ?? '-') }}</small>
                            </td>
                            <td>
                                <strong>{{ $pinjam->buku->judul ?? '-' }}</strong>
                                <br><small style="color: #777;">Penulis: {{ $pinjam->buku->penulis ?? '-' }}</small>
                            </td>
                            <td>{{ $pinjam->tanggal_pinjam }}</td>
                            <td>{{ $pinjam->tanggal_kembali ?? '-' }}</td>
                            <td>
                                @if ($pinjam->status === 'disetujui')
                                    <span class="badge badge-success">Sedang Dipinjam</span>
                                @elseif ($pinjam->status === 'menunggu')
                                    <span class="badge badge-warning">Menunggu</span>
                                @elseif ($pinjam->status === 'dikembalikan')
                                    <span class="badge badge-info">Selesai Dikembalikan</span>
                                @else
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if ($pinjam->status === 'menunggu')
                                    @can('Memproses persetujuan peminjaman')
                                        <form method="POST" action="{{ route('peminjaman.approve', $pinjam->id) }}" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm">Setujui</button>
                                        </form>
                                        <form method="POST" action="{{ route('peminjaman.reject', $pinjam->id) }}" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-danger btn-sm">Tolak</button>
                                        </form>
                                    @endcan
                                @elseif ($pinjam->status === 'disetujui')
                                    @can('Mencatat pengembalian buku')
                                        <form method="POST" action="{{ route('peminjaman.return', $pinjam->id) }}" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm">Catat Pengembalian</button>
                                        </form>
                                    @endcan
                                @else
                                    <span style="color: #888; font-size: 13px;">Transaksi Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 24px;">Tidak ada catatan peminjaman pada filter ini.</td>
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

