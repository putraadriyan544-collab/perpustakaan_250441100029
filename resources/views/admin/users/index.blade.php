@extends('layouts.app')

@section('title', 'Kelola Pengguna & Role')

@section('content')
<div>
    <h2 class="judul-halaman">Kelola Data Pengguna & Penetapan Role</h2>
    <p class="subjudul">Manajemen akun pengguna sistem dan pengaturan Role-Based Access Control (RBAC).</p>

    <!-- Formulir Tambah Pengguna Baru -->
    <div class="card">
        <div class="card-header">Tambah Pengguna Baru</div>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" id="name" class="form-control" required placeholder="Contoh: Rian Pratama" value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">Alamat Email</label>
                    <input type="email" name="email" id="email" class="form-control" required placeholder="email@perpustakaan.com" value="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <input type="password" name="password" id="password" class="form-control" required placeholder="Minimal 8 karakter">
                </div>
                <div class="form-group">
                    <label for="role" class="form-label">Penetapan Role</label>
                    <select name="role" id="role" class="form-select" required>
                        <option value="siswa" {{ old('role') === 'siswa' ? 'selected' : '' }}>Siswa (Member)</option>
                        <option value="petugas" {{ old('role') === 'petugas' ? 'selected' : '' }}>Petugas Perpustakaan</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                </div>
            </div>

            <div style="margin-top: 10px;">
                <button type="submit" class="btn btn-success">Simpan Pengguna & Role</button>
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Pengguna -->
    <div class="card">
        <div class="card-header">Daftar Pengguna Terdaftar</div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Pengguna</th>
                        <th>Email</th>
                        <th>Role Saat Ini</th>
                        <th style="width: 280px;">Ubah Role</th>
                        <th style="text-align: center; width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $index => $u)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $u->name }}</strong>
                                @if ($u->id === Auth::id())
                                    <span class="badge badge-info" style="margin-left: 6px;">(Akun Anda)</span>
                                @endif
                            </td>
                            <td>{{ $u->email }}</td>
                            <td>
                                @php
                                    $roleName = $u->getRoleNames()->first() ?? 'Tidak ada';
                                @endphp
                                @if ($roleName === 'admin')
                                    <span class="badge" style="background: #5c3d2e; color: #fff;">ADMIN</span>
                                @elseif ($roleName === 'petugas')
                                    <span class="badge badge-warning">PETUGAS</span>
                                @else
                                    <span class="badge badge-success">SISWA</span>
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.users.updateRole', $u->id) }}" style="display: flex; gap: 8px; align-items: center;">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" class="form-select" style="padding: 4px 8px; font-size: 13px;">
                                        <option value="siswa" {{ $roleName === 'siswa' ? 'selected' : '' }}>Siswa</option>
                                        <option value="petugas" {{ $roleName === 'petugas' ? 'selected' : '' }}>Petugas</option>
                                        <option value="admin" {{ $roleName === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline">Terapkan</button>
                                </form>
                            </td>
                            <td style="text-align: center;">
                                @if ($u->id !== Auth::id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $u->name }}?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                @else
                                    <span style="color: #999; font-size: 12px;">Aktif</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

