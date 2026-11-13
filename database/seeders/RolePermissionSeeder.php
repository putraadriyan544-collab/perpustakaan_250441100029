<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 16 Daftar Permission sesuai Modul 5 Soal 4
        $permissions = [
            'Akses dashboard admin',               // 1
            'Akses dashboard petugas',             // 2
            'Lihat daftar buku',                   // 3
            'Kelola data buku',                    // 4
            'Kelola data pengguna',                // 5
            'Kelola role pengguna',                // 6
            'Kelola peminjaman',                   // 7
            'Kelola pengembalian',                 // 8
            'Mengajukan peminjaman buku',          // 9
            'Memproses persetujuan peminjaman',    // 10
            'Mencatat pengembalian buku',          // 11
            'Melihat status peminjaman sendiri',   // 12
            'Melihat riwayat peminjaman sendiri',  // 13
            'Lihat laporan perpustakaan',          // 14
            'Lihat profil sendiri',                // 15
            'Ubah profil sendiri',                 // 16
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // 1. Role Admin: 15 permission (semua kecuali No. 2 'Akses dashboard petugas')
        $roleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminPermissions = array_filter($permissions, fn ($p) => $p !== 'Akses dashboard petugas');
        $roleAdmin->syncPermissions($adminPermissions);

        // 2. Role Petugas: 13 permission (semua kecuali No. 1, 5, 6)
        $rolePetugas = Role::firstOrCreate(['name' => 'petugas', 'guard_name' => 'web']);
        $petugasPermissions = array_filter(
            $permissions,
            fn ($p) => !in_array($p, ['Akses dashboard admin', 'Kelola data pengguna', 'Kelola role pengguna'])
        );
        $rolePetugas->syncPermissions($petugasPermissions);

        // 3. Role Siswa: 6 permission (No. 3, 9, 12, 13, 15, 16)
        $roleSiswa = Role::firstOrCreate(['name' => 'siswa', 'guard_name' => 'web']);
        $siswaPermissions = [
            'Lihat daftar buku',
            'Mengajukan peminjaman buku',
            'Melihat status peminjaman sendiri',
            'Melihat riwayat peminjaman sendiri',
            'Lihat profil sendiri',
            'Ubah profil sendiri',
        ];
        $roleSiswa->syncPermissions($siswaPermissions);

        // Buat Akun Pengguna Default untuk Pengujian

        // Akun Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@perpustakaan.com'],
            [
                'name' => 'Administrator Perpustakaan',
                'password' => Hash::make('password'),
            ]
        );
        $admin->syncRoles(['admin']);

        // Akun Petugas
        $petugas = User::firstOrCreate(
            ['email' => 'petugas@perpustakaan.com'],
            [
                'name' => 'Petugas Perpustakaan',
                'password' => Hash::make('password'),   
            ]
        );
        $petugas->syncRoles(['petugas']);

        // Akun Siswa (Pengguna Utama)
        $siswa = User::firstOrCreate(
            ['email' => 'siswa@perpustakaan.com'],
            [
                'name' => 'Ahmad Siswa',
                'password' => Hash::make('password'),
            ]
        );
        $siswa->syncRoles(['siswa']);

        // Akun Siswa Lain (Untuk Uji Skenario 14: Siswa lain)
        $siswaLain = User::firstOrCreate(
            ['email' => 'budi@perpustakaan.com'],
            [
                'name' => 'Budi Siswa Lain',
                'password' => Hash::make('password'),
            ]
        );
        $siswaLain->syncRoles(['siswa']);

        // Contoh Data Peminjaman untuk Siswa dan Siswa Lain
        $bukuPertama = Buku::first();
        $bukuKedua = Buku::skip(1)->first();

        if ($bukuPertama) {
            Peminjaman::firstOrCreate(
                [
                    'user_id' => $siswa->id,
                    'buku_id' => $bukuPertama->id,
                ],
                [
                    'tanggal_pinjam' => now()->subDays(3)->toDateString(),
                    'tanggal_kembali' => null,
                    'status' => 'disetujui',
                ]
            );
        }

        if ($bukuKedua) {
            Peminjaman::firstOrCreate(
                [
                    'user_id' => $siswaLain->id,
                    'buku_id' => $bukuKedua->id,
                ],
                [
                    'tanggal_pinjam' => now()->subDays(5)->toDateString(),
                    'tanggal_kembali' => null,
                    'status' => 'disetujui',
                ]
            );
        }
    }
}
