@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <div class="hero">
        <h2>Selamat Datang di Perpustakaan Digital</h2>
        <p>Temukan berbagai koleksi buku menarik mulai dari novel, sejarah, filsafat, hingga teknologi.</p>
        <a href="{{ route('buku.index') }}" class="btn">Jelajahi Koleksi Buku</a>
    </div>

    <div class="fitur">
        <div class="item">
            <h3>Koleksi Lengkap</h3>
            <p>Beragam kategori buku tersedia untuk semua kalangan.</p>
        </div>
        <div class="item">
            <h3>Mudah Dicari</h3>
            <p>Lihat detail setiap buku hanya dengan satu klik.</p>
        </div>
        <div class="item">
            <h3>Akses Kapan Saja</h3>
            <p>Perpustakaan digital yang siap diakses kapan pun.</p>
        </div>
    </div>
@endsection