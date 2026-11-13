@extends('layouts.app')

@section('title', 'Detail Peminjaman Saya')

@section('content')
<div class="detail">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">Detail Transaksi Peminjaman #{{ $peminjaman->id }}</h2>
        @if ($peminjaman->status === 'disetujui')
            <span class="badge badge-success">Sedang Dipinjam</span>
        @elseif ($peminjaman->status === 'menunggu')
            <span class="badge badge-warning">Menunggu Persetujuan</span>
        @elseif ($peminjaman->status === 'dikembalikan')
            <span class="badge badge-info">Sudah Dikembalikan</span>
        @else
            <span class="badge badge-danger">Ditolak</span>
        @endif
    </div>

    <table>
        <tr>
            <th>Peminjam (Anda)</th>
            <td>{{ Auth::user()->name }} ({{ Auth::user()->email }})</td>
        </tr>
        <tr>
            <th>Judul Buku</th>
            <td><strong>{{ $peminjaman->buku->judul }}</strong></td>
        </tr>
        <tr>
            <th>Penulis Buku</th>
            <td>{{ $peminjaman->buku->penulis }}</td>
        </tr>
        <tr>
            <th>Kategori Buku</th>
            <td>{{ $peminjaman->buku->kategori->nama ?? '-' }}</td>
        </tr>
        <tr>
            <th>Tahun Terbit Buku</th>
            <td>{{ $peminjaman->buku->tahun_terbit }}</td>
        </tr>
        <tr>
            <th>Tanggal Pengajuan/Pinjam</th>
            <td>{{ $peminjaman->tanggal_pinjam }}</td>
        </tr>
        <tr>
            <th>Tanggal Dikembalikan</th>
            <td>{{ $peminjaman->tanggal_kembali ?? 'Belum dikembalikan' }}</td>
        </tr>
        <tr>
            <th>Status Peminjaman</th>
            <td>{{ ucfirst($peminjaman->status) }}</td>
        </tr>
    </table>

    <div style="margin-top: 24px;">
        <a href="{{ route('siswa.peminjaman.index') }}" class="btn btn-secondary btn-sm">← Kembali ke Riwayat Saya</a>
    </div>
</div>
@endsection

