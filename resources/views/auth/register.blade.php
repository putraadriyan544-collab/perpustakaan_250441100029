@extends('layouts.app')

@section('title', 'Pendaftaran Siswa Baru')

@section('content')
<div class="auth-box">
    <h2>Daftar Akun Baru</h2>
    <p class="subtitle">Pendaftaran Akun Anggota Perpustakaan Digital</p>

    @if ($errors->any())
        <div class="alert alert-danger" style="display: block;">
            <strong>Pendaftaran Gagal:</strong>
            <ul style="margin-left: 20px; margin-top: 6px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Nama lengkap siswa">
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Alamat Email</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autocomplete="username" placeholder="siswa@sekolah.sch.id">
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Kata Sandi</label>
            <input type="password" id="password" name="password" class="form-control" required autocomplete="new-password" placeholder="Minimal 8 karakter">
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required autocomplete="new-password" placeholder="Ulangi kata sandi">
        </div>

        <div style="margin-top: 24px;">
            <button type="submit" class="btn" style="width: 100%;">Daftar Sebagai Siswa</button>
        </div>

        <div style="text-align: center; margin-top: 20px; font-size: 14px; color: #666;">
            Sudah memiliki akun? <a href="{{ route('login') }}" style="color: #c9973f; font-weight: 600;">Masuk di sini</a>
        </div>
    </form>
</div>
@endsection
