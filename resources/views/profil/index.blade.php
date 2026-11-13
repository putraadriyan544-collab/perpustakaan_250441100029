@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
<div style="max-width: 680px; margin: 0 auto;">
    <h2 class="judul-halaman">Profil Akun Saya</h2>
    <p class="subjudul">Kelola informasi pribadi dan perbarui kata sandi akun perpustakaan Anda.</p>

    @if ($errors->any())
        <div class="alert alert-danger" style="display: block;">
            <strong>Perhatian:</strong>
            <ul style="margin-left: 20px; margin-top: 6px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">Informasi Profil & Hak Akses</div>

        <div style="margin-bottom: 20px; padding: 12px 16px; background: #fdfaf4; border-radius: 8px; border: 1px solid #f1e2c8;">
            <p style="margin-bottom: 4px; font-size: 14px;">
                <strong>Role Pengguna:</strong> 
                <span class="badge" style="background: #5c3d2e; color: #fff;">{{ strtoupper($user->getRoleNames()->first() ?? 'PENGGUNA') }}</span>
            </p>
            <p style="font-size: 13px; color: #666; margin-top: 6px;">
                Role Anda diatur secara terpusat oleh administrator dan menentukan fitur yang dapat Anda akses.
            </p>
        </div>

        <form method="POST" action="{{ route('profil.update') }}">
            @csrf
            @method('PATCH')

            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Alamat Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>

            <hr style="border: 0; border-top: 1px solid #eee; margin: 24px 0;">

            <h3 style="color: #5c3d2e; font-size: 16px; margin-bottom: 6px;">Ubah Kata Sandi (Opsional)</h3>
            <p style="color: #777; font-size: 13px; margin-bottom: 16px;">Biarkan kosong jika Anda tidak ingin mengganti kata sandi.</p>

            <div class="form-group">
                <label for="password" class="form-label">Kata Sandi Baru</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 8 karakter">
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru">
            </div>

            <div style="margin-top: 24px;">
                <button type="submit" class="btn">Simpan Perubahan Profil</button>
            </div>
        </form>
    </div>
</div>
@endsection

