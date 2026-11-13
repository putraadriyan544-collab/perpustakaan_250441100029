@extends('layouts.app')

@section('title', 'Masuk ke Sistem Perpustakaan')

@section('content')
<div class="auth-box">
    <h2>Masuk ke Akun</h2>
    <p class="subtitle">Silakan masukkan email dan kata sandi Anda</p>

    @if ($errors->any())
        <div class="alert alert-danger" style="display: block;">
            <strong>Gagal Masuk:</strong>
            <ul style="margin-left: 20px; margin-top: 6px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Alamat Email</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@email.com">
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Kata Sandi</label>
            <input type="password" id="password" name="password" class="form-control" required autocomplete="current-password" placeholder="••••••••">
        </div>

        <div class="form-group" style="display: flex; align-items: center; justify-content: space-between; margin-top: 10px;">
            <label style="display: flex; align-items: center; gap: 8px; font-size: 14px; cursor: pointer;">
                <input type="checkbox" name="remember" id="remember_me">
                Ingat Saya
            </label>
        </div>

        <div style="margin-top: 24px;">
            <button type="submit" class="btn" style="width: 100%;">Masuk Sekarang</button>
        </div>

        <div style="text-align: center; margin-top: 20px; font-size: 14px; color: #666;">
            Belum memiliki akun? <a href="{{ route('register') }}" style="color: #c9973f; font-weight: 600;">Daftar di sini</a>
        </div>
    </form>
</div>
@endsection
