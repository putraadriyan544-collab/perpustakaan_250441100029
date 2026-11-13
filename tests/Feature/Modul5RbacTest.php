<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Modul5RbacTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $petugas;
    protected User $siswa;
    protected User $siswaLain;
    protected Kategori $kategori;
    protected Buku $buku;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kategori = Kategori::create(['nama' => 'Teknologi Informasi']);
        $this->buku = Buku::create([
            'kategori_id' => $this->kategori->id,
            'judul' => 'Belajar Laravel RBAC',
            'penulis' => 'Programmer Handal',
            'tahun_terbit' => 2026,
        ]);

        $this->seed(RolePermissionSeeder::class);

        $this->admin = User::where('email', 'admin@perpustakaan.com')->first();
        $this->petugas = User::where('email', 'petugas@perpustakaan.com')->first();
        $this->siswa = User::where('email', 'siswa@perpustakaan.com')->first();
        $this->siswaLain = User::where('email', 'budi@perpustakaan.com')->first();
    }

    /**
     * Skenario 1: Belum login membuka halaman dashboard admin.
     * Hasil yang diharapkan: dialihkan ke halaman login.
     */
    public function test_skenario_01_guest_membuka_dashboard_admin_dialihkan_ke_login(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    /**
     * Skenario 2: Admin membuka dashboard admin.
     * Hasil yang diharapkan: dashboard admin dapat diakses.
     */
    public function test_skenario_02_admin_membuka_dashboard_admin_berhasil(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Administrator');
    }

    /**
     * Skenario 3: Admin menambah, mengubah, dan menghapus data buku.
     * Hasil yang diharapkan: operasi berhasil.
     */
    public function test_skenario_03_admin_menambah_mengubah_dan_menghapus_data_buku(): void
    {
        // 1. Tambah Buku
        $tambahResponse = $this->actingAs($this->admin)->post('/buku', [
            'kategori_id' => $this->kategori->id,
            'judul' => 'Buku Baru Admin',
            'penulis' => 'Penulis Admin',
            'tahun_terbit' => 2025,
        ]);
        $tambahResponse->assertRedirect('/buku');
        $this->assertDatabaseHas('bukus', ['judul' => 'Buku Baru Admin']);

        $bukuBaru = Buku::where('judul', 'Buku Baru Admin')->first();

        // 2. Ubah Buku
        $ubahResponse = $this->actingAs($this->admin)->put("/buku/{$bukuBaru->id}", [
            'kategori_id' => $this->kategori->id,
            'judul' => 'Buku Admin Diperbarui',
            'penulis' => 'Penulis Admin Baru',
            'tahun_terbit' => 2026,
        ]);
        $ubahResponse->assertRedirect('/buku');
        $this->assertDatabaseHas('bukus', ['judul' => 'Buku Admin Diperbarui']);

        // 3. Hapus Buku
        $hapusResponse = $this->actingAs($this->admin)->delete("/buku/{$bukuBaru->id}");
        $hapusResponse->assertRedirect('/buku');
        $this->assertDatabaseMissing('bukus', ['id' => $bukuBaru->id]);
    }

    /**
     * Skenario 4: Admin mengelola akun dan role pengguna.
     * Hasil yang diharapkan: operasi berhasil.
     */
    public function test_skenario_04_admin_mengelola_akun_dan_role_pengguna(): void
    {
        // Akses daftar user
        $aksesResponse = $this->actingAs($this->admin)->get('/admin/users');
        $aksesResponse->assertStatus(200);

        // Tambah user baru oleh admin
        $tambahUserResponse = $this->actingAs($this->admin)->post('/admin/users', [
            'name' => 'Staf Baru',
            'email' => 'stafbaru@perpustakaan.com',
            'password' => 'password123',
            'role' => 'petugas',
        ]);
        $tambahUserResponse->assertRedirect('/admin/users');
        $this->assertDatabaseHas('users', ['email' => 'stafbaru@perpustakaan.com']);

        $staf = User::where('email', 'stafbaru@perpustakaan.com')->first();
        $this->assertTrue($staf->hasRole('petugas'));

        // Ubah role user oleh admin
        $ubahRoleResponse = $this->actingAs($this->admin)->patch("/admin/users/{$staf->id}/role", [
            'role' => 'admin',
        ]);
        $ubahRoleResponse->assertRedirect('/admin/users');
        $staf->refresh();
        $this->assertTrue($staf->hasRole('admin'));

        // Hapus user oleh admin
        $hapusResponse = $this->actingAs($this->admin)->delete("/admin/users/{$staf->id}");
        $hapusResponse->assertRedirect('/admin/users');
        $this->assertDatabaseMissing('users', ['id' => $staf->id]);
    }

    /**
     * Skenario 5: Admin mengakses data peminjaman, pengembalian, dan laporan.
     * Hasil yang diharapkan: data dapat diakses sesuai hak akses.
     */
    public function test_skenario_05_admin_mengakses_data_peminjaman_pengembalian_dan_laporan(): void
    {
        $peminjamanResponse = $this->actingAs($this->admin)->get('/peminjaman');
        $peminjamanResponse->assertStatus(200);

        $laporanResponse = $this->actingAs($this->admin)->get('/laporan');
        $laporanResponse->assertStatus(200);
    }

    /**
     * Skenario 6: Petugas membuka dashboard petugas.
     * Hasil yang diharapkan: dashboard petugas dapat diakses.
     */
    public function test_skenario_06_petugas_membuka_dashboard_petugas_berhasil(): void
    {
        $response = $this->actingAs($this->petugas)->get('/petugas/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Operasional Petugas');
    }

    /**
     * Skenario 7: Petugas mengelola data buku.
     * Hasil yang diharapkan: operasi berhasil.
     */
    public function test_skenario_07_petugas_mengelola_data_buku(): void
    {
        // 1. Petugas Tambah Buku
        $tambah = $this->actingAs($this->petugas)->post('/buku', [
            'kategori_id' => $this->kategori->id,
            'judul' => 'Buku Entri Petugas',
            'penulis' => 'Penulis Petugas',
            'tahun_terbit' => 2024,
        ]);
        $tambah->assertRedirect('/buku');
        $this->assertDatabaseHas('bukus', ['judul' => 'Buku Entri Petugas']);

        $buku = Buku::where('judul', 'Buku Entri Petugas')->first();

        // 2. Petugas Ubah Buku
        $ubah = $this->actingAs($this->petugas)->put("/buku/{$buku->id}", [
            'kategori_id' => $this->kategori->id,
            'judul' => 'Buku Petugas Diedit',
            'penulis' => 'Penulis Petugas',
            'tahun_terbit' => 2024,
        ]);
        $ubah->assertRedirect('/buku');
        $this->assertDatabaseHas('bukus', ['judul' => 'Buku Petugas Diedit']);

        // 3. Petugas Hapus Buku
        $hapus = $this->actingAs($this->petugas)->delete("/buku/{$buku->id}");
        $hapus->assertRedirect('/buku');
        $this->assertDatabaseMissing('bukus', ['id' => $buku->id]);
    }

    /**
     * Skenario 8: Petugas memproses peminjaman dan pengembalian.
     * Hasil yang diharapkan: operasi berhasil.
     */
    public function test_skenario_08_petugas_memproses_peminjaman_dan_pengembalian(): void
    {
        $pinjam = Peminjaman::create([
            'user_id' => $this->siswa->id,
            'buku_id' => $this->buku->id,
            'tanggal_pinjam' => now()->toDateString(),
            'status' => 'menunggu',
        ]);

        // 1. Petugas menyetujui peminjaman
        $setujui = $this->actingAs($this->petugas)->patch("/peminjaman/{$pinjam->id}/approve");
        $setujui->assertRedirect();
        $pinjam->refresh();
        $this->assertEquals('disetujui', $pinjam->status);

        // 2. Petugas mencatat pengembalian
        $kembali = $this->actingAs($this->petugas)->patch("/peminjaman/{$pinjam->id}/return");
        $kembali->assertRedirect();
        $pinjam->refresh();
        $this->assertEquals('dikembalikan', $pinjam->status);
        $this->assertNotNull($pinjam->tanggal_kembali);
    }

    /**
     * Skenario 9: Petugas membuka halaman pengelolaan akun atau role pengguna.
     * Hasil yang diharapkan: akses ditolak (403 Forbidden).
     */
    public function test_skenario_09_petugas_membuka_halaman_kelola_akun_role_ditolak(): void
    {
        $response = $this->actingAs($this->petugas)->get('/admin/users');

        $response->assertStatus(403);
    }

    /**
     * Skenario 10: Siswa melihat daftar dan detail buku.
     * Hasil yang diharapkan: data buku dapat dilihat.
     */
    public function test_skenario_10_siswa_melihat_daftar_dan_detail_buku(): void
    {
        $daftarResponse = $this->actingAs($this->siswa)->get('/buku');
        $daftarResponse->assertStatus(200);

        $detailResponse = $this->actingAs($this->siswa)->get("/buku/{$this->buku->id}");
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee($this->buku->judul);
    }

    /**
     * Skenario 11: Siswa melihat dan mengubah profil sendiri.
     * Hasil yang diharapkan: data profil milik sendiri dapat diakses dan diperbarui.
     */
    public function test_skenario_11_siswa_melihat_dan_mengubah_profil_sendiri(): void
    {
        $lihat = $this->actingAs($this->siswa)->get('/profil');
        $lihat->assertStatus(200);
        $lihat->assertSee($this->siswa->email);

        $ubah = $this->actingAs($this->siswa)->patch('/profil', [
            'name' => 'Ahmad Siswa Updated',
            'email' => $this->siswa->email,
        ]);
        $ubah->assertRedirect();
        $this->siswa->refresh();
        $this->assertEquals('Ahmad Siswa Updated', $this->siswa->name);
    }

    /**
     * Skenario 12: Siswa melihat riwayat peminjaman sendiri.
     * Hasil yang diharapkan: riwayat milik sendiri dapat dilihat.
     */
    public function test_skenario_12_siswa_melihat_riwayat_peminjaman_sendiri(): void
    {
        $pinjam = Peminjaman::create([
            'user_id' => $this->siswa->id,
            'buku_id' => $this->buku->id,
            'tanggal_pinjam' => now()->toDateString(),
            'status' => 'disetujui',
        ]);

        $response = $this->actingAs($this->siswa)->get('/siswa/peminjaman');
        $response->assertStatus(200);
        $response->assertSee($this->buku->judul);
    }

    /**
     * Skenario 13: Siswa membuka dashboard admin atau halaman pengelolaan buku.
     * Hasil yang diharapkan: akses ditolak (403 Forbidden).
     */
    public function test_skenario_13_siswa_membuka_dashboard_admin_atau_kelola_buku_ditolak(): void
    {
        // 1. Siswa coba buka dashboard admin
        $adminDashboard = $this->actingAs($this->siswa)->get('/admin/dashboard');
        $adminDashboard->assertStatus(403);

        // 2. Siswa coba tambah buku via endpoint kelola buku
        $tambahBuku = $this->actingAs($this->siswa)->post('/buku', [
            'kategori_id' => $this->kategori->id,
            'judul' => 'Buku Ilegal Siswa',
            'penulis' => 'Siswa',
            'tahun_terbit' => 2026,
        ]);
        $tambahBuku->assertStatus(403);
    }

    /**
     * Skenario 14: Siswa mencoba melihat riwayat peminjaman siswa lain.
     * Hasil yang diharapkan: akses ditolak (403 Forbidden).
     */
    public function test_skenario_14_siswa_mencoba_melihat_riwayat_peminjaman_siswa_lain_ditolak(): void
    {
        // Pinjaman milik Siswa Lain (Budi)
        $pinjamSiswaLain = Peminjaman::create([
            'user_id' => $this->siswaLain->id,
            'buku_id' => $this->buku->id,
            'tanggal_pinjam' => now()->toDateString(),
            'status' => 'disetujui',
        ]);

        // Siswa (Ahmad) mencoba mengakses URL detail riwayat pinjam milik Budi
        $response = $this->actingAs($this->siswa)->get("/siswa/peminjaman/{$pinjamSiswaLain->id}");

        $response->assertStatus(403);
    }

    /**
     * Skenario 15: Admin/Petugas/Siswa melakukan logout lalu membuka halaman yang dilindungi.
     * Hasil yang diharapkan: pengguna harus login kembali (dialihkan ke login).
     */
    public function test_skenario_15_logout_lalu_membuka_halaman_dilindungi_dialihkan_ke_login(): void
    {
        // Siswa login
        $this->actingAs($this->siswa);

        // Siswa logout
        $logoutResponse = $this->post('/logout');
        $logoutResponse->assertRedirect('/');
        $this->assertGuest();

        // Mencoba membuka halaman terlindungi setelah logout
        $protectedResponse = $this->get('/siswa/peminjaman');
        $protectedResponse->assertRedirect('/login');

        // Mencoba membuka halaman admin setelah logout
        $adminProtectedResponse = $this->get('/admin/dashboard');
        $adminProtectedResponse->assertRedirect('/login');
    }
}

