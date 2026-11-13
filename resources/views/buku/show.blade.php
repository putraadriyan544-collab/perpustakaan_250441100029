@extends('layouts.app')

@section('title', $buku->judul)

@section('content')
    <div class="detail">
        <span class="badge">{{ $buku->kategori->nama ?? 'Umum' }}</span>
        <h2>{{ $buku->judul }}</h2>

        <table>
            <tr><th>ID Buku</th><td>{{ $buku->id }}</td></tr>
            <tr><th>Judul</th><td>{{ $buku->judul }}</td></tr>
            <tr><th>Penulis</th><td>{{ $buku->penulis }}</td></tr>
            <tr><th>Tahun Terbit</th><td>{{ $buku->tahun_terbit }}</td></tr>
            <tr><th>Kategori</th><td>{{ $buku->kategori->nama ?? '-' }}</td></tr>
        </table>

        <div style="display: flex; gap: 12px; margin-top: 24px; align-items: center; flex-wrap: wrap;">
            <a href="{{ route('buku.index') }}" class="btn btn-secondary btn-sm">← Kembali ke Daftar Buku</a>

            @auth
                @can('Mengajukan peminjaman buku')
                    <form action="{{ route('siswa.peminjaman.store') }}" method="POST" style="display: inline; margin: 0;">
                        @csrf
                        <input type="hidden" name="buku_id" value="{{ $buku->id }}">
                        <button type="submit" class="btn btn-sm">Ajukan Pinjam Buku Ini</button>
                    </form>
                @endcan
            @else
                <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Masuk untuk Meminjam</a>
            @endauth
        </div>
    </div>
@endsection
