<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Perpustakaan Digital')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <header class="header">
        <h1>Perpustakaan Digital</h1>
        <p>Jendela dunia ada di genggamanmu</p>
    </header>

    <nav class="navbar">
        <div class="nav-inner">
            <div class="nav-left">
                <a href="{{ route('beranda') }}" class="{{ request()->routeIs('beranda') ? 'active' : '' }}">Beranda</a>
                <a href="{{ route('buku.index') }}" class="{{ request()->routeIs('buku.*') ? 'active' : '' }}">Daftar Buku</a>

                @auth
                    @role('admin')
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard Admin</a>
                        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Kelola Pengguna</a>
                        <a href="{{ route('peminjaman.index') }}" class="{{ request()->routeIs('peminjaman.*') ? 'active' : '' }}">Peminjaman & Pengembalian</a>
                        <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}">Laporan</a>
                    @endrole

                    @role('petugas')
                        <a href="{{ route('petugas.dashboard') }}" class="{{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">Dashboard Petugas</a>
                        <a href="{{ route('peminjaman.index') }}" class="{{ request()->routeIs('peminjaman.*') ? 'active' : '' }}">Peminjaman & Pengembalian</a>
                        <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}">Laporan</a>
                    @endrole

                    @role('siswa')
                        <a href="{{ route('siswa.peminjaman.index') }}" class="{{ request()->routeIs('siswa.peminjaman.*') ? 'active' : '' }}">Riwayat Peminjaman</a>
                    @endrole
                @endauth
            </div>

            <div class="nav-right">
                @guest
                    <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">Masuk (Login)</a>
                    <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'active' : '' }}">Daftar</a>
                @else
                    <a href="{{ route('profil.index') }}" class="{{ request()->routeIs('profil.*') ? 'active' : '' }}">
                        Profil Saya ({{ Auth::user()->name }} [{{ strtoupper(Auth::user()->getRoleNames()->first() ?? 'PENGGUNA') }}])
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline; margin: 0;">
                        @csrf
                        <button type="submit" class="nav-logout-btn">Keluar</button>
                    </form>
                @endguest
            </div>
        </div>
    </nav>

    <main>
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="footer">
        © 2026 Perpustakaan Digital — 250441100029
    </footer>
</body>
</html>
