@extends('layouts.app')

@section('title', 'Daftar Buku')

@section('content')
<div>
    <h2 class="judul-halaman">Koleksi & Daftar Buku</h2>
    <p class="subjudul">Jelajahi dan temukan berbagai judul buku yang tersedia di perpustakaan digital.</p>

    <!-- Wadah Pesan Status / Error Validasi -->
    <div id="alert-container"></div>

    {{-- @can('Kelola data buku') sebelum nomer 3 --}}

    @can('kelola-buku')  
    <!-- Formulir Tambah / Ubah Buku (Hanya untuk Admin dan Petugas) -->
    <div class="card">
        <div class="card-header" id="form-title">Tambah Buku Baru</div>
        <form id="form-buku">
            <!-- ID Tersembunyi (Digunakan saat mode edit/UPDATE) -->
            <input type="hidden" id="buku-id" value="">

            <div class="form-row">
                <div class="form-group">
                    <label for="kategori_id" class="form-label">ID Kategori</label>
                    <input type="number" class="form-control" id="kategori_id" required placeholder="Contoh: 1">
                </div>
                <div class="form-group">
                    <label for="judul" class="form-label">Judul Buku</label>
                    <input type="text" class="form-control" id="judul" required placeholder="Judul buku">
                </div>
                <div class="form-group">
                    <label for="penulis" class="form-label">Penulis</label>
                    <input type="text" class="form-control" id="penulis" required placeholder="Nama penulis">
                </div>
                <div class="form-group">
                    <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
                    <input type="number" class="form-control" id="tahun_terbit" placeholder="Contoh: 2024" required>
                </div>
            </div>
            
            <div style="margin-top: 10px;">
                <button type="submit" class="btn btn-success" id="btn-simpan">Simpan Data</button>
                <button type="button" class="btn btn-secondary" style="display: none;" id="btn-batal" onclick="resetForm()">Batal</button>
            </div>
        </form>
    </div>
    @endcan

    <!-- Tabel Daftar Buku -->
    <div class="card">
        <div class="card-header">Daftar Koleksi Buku Perpustakaan</div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Kategori</th>
                        <th>Judul Buku</th>
                        <th>Penulis</th>
                        <th>Tahun</th>
                        <th style="text-align: center; width: 220px;">Aksi</th>
                    </tr>
                </thead>
                 <!-- batasperalihan modul 5 ke 6 -->
                <tbody id="buku-table-body">
    @forelse ($bukus as $index => $buku)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td><span class="badge">{{ $buku->kategori->nama ?? '-' }}</span></td>
            <td><strong>{{ $buku->judul }}</strong></td> <!-- Celah A1: tidak di-escape -->
            <td>{{ $buku->penulis }}</td>
            <td>{{ $buku->tahun_terbit }}</td>
            <td style="text-align: center;">
                <a href="{{ route('buku.show', $buku->id) }}" class="btn btn-outline btn-sm">Detail</a>

                @can('kelola-buku')
                    <button class="btn btn-warning btn-sm" onclick='editBuku(@json($buku))'>Ubah</button>
                    <form action="{{ route('buku.destroy', $buku->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">Hapus</button>
                    </form>
                @endcan

                @can('Mengajukan peminjaman buku')
                    <form action="{{ route('siswa.peminjaman.store') }}" method="POST" style="display: inline;">
                        @csrf
                        <input type="hidden" name="buku_id" value="{{ $buku->id }}">
                        <button type="submit" class="btn btn-sm">Pinjam</button>
                    </form>
                @endcan
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" style="text-align: center; padding: 20px;">Belum ada data buku.</td>
        </tr>
    @endforelse
</tbody>
                <!-- <tbody id="buku-table-body">
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px;">Memuat data buku...</td>
                    </tr>
                </tbody> untuk peralihan modul 5 ke 6 -->
            </table>
        </div>
    </div>
</div>

<script>
    const canManageBuku = @json(auth()->check() && auth()->user()->can('Kelola data buku'));
    const canBorrow = @json(auth()->check() && auth()->user()->can('Mengajukan peminjaman buku'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    document.addEventListener('DOMContentLoaded', () => {
        // loadBuku();

        const formBuku = document.getElementById('form-buku');
        if (formBuku) {
            formBuku.addEventListener('submit', function(e) {
                e.preventDefault();
                saveBuku();
            });
        }
    });

    async function loadBuku() {
        const tbody = document.getElementById('buku-table-body');
        
        try {
            const response = await fetch('/api/buku', {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) {
                throw new Error(`Gagal mengambil data. Status: ${response.status}`);
            }

            const result = await response.json();
            const dataBuku = result.data;

            if (!dataBuku || dataBuku.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 20px;">Belum ada data buku.</td></tr>`;
                return;
            }

            let html = '';
            dataBuku.forEach((buku, index) => {
                const bukuJson = JSON.stringify(buku).replace(/'/g, "&apos;");

                let aksiHtml = `<a href="/buku/${buku.id}" class="btn btn-outline btn-sm" style="margin-right: 4px;">Detail</a>`;

                if (canManageBuku) {
                    aksiHtml += `
                        <button class="btn btn-warning btn-sm" style="margin-right: 4px;" onclick='editBuku(${bukuJson})'>Ubah</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteBuku(${buku.id})">Hapus</button>
                    `;
                } else if (canBorrow) {
                    aksiHtml += `
                        <form action="{{ route('siswa.peminjaman.store') }}" method="POST" style="display: inline;">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="buku_id" value="${buku.id}">
                            <button type="submit" class="btn btn-sm">Pinjam</button>
                        </form>
                    `;
                }

                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td><span class="badge">${buku.kategori || '-'}</span></td>
                        <td><strong>${buku.judul}</strong></td>
                        <td>${buku.penulis}</td>
                        <td>${buku.tahun_terbit}</td>
                        <td style="text-align: center;">${aksiHtml}</td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;

        } catch (error) {
            showAlert(error.message, 'danger');
            tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; color: #c62828;">Gagal memuat data buku.</td></tr>`;
        }
    }

    async function saveBuku() {
        const id = document.getElementById('buku-id').value;
        const payload = {
            kategori_id: document.getElementById('kategori_id').value,
            judul: document.getElementById('judul').value,
            penulis: document.getElementById('penulis').value,
            tahun_terbit: document.getElementById('tahun_terbit').value,
        };

        const isEdit = id !== '' && id !== null && id !== undefined;
        const url = isEdit ? `/api/buku/${id}` : '/api/buku';
        const method = isEdit ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (!response.ok) {
                if (response.status === 422) {
                    let errorMessages = Object.values(result.errors).flat().join('<br>');
                    throw new Error(errorMessages);
                }
                throw new Error(result.message || 'Terjadi kesalahan pada server.');
            }

            showAlert(isEdit ? 'Buku berhasil diperbarui!' : 'Buku berhasil ditambahkan!', 'success');
            resetForm();
            window.location.reload();// loadBuku();

        } catch (error) {
            showAlert(error.message, 'danger');
        }
    }

    function editBuku(buku) {
        document.getElementById('buku-id').value = buku.id;
        document.getElementById('kategori_id').value = buku.kategori_id;
        document.getElementById('judul').value = buku.judul;
        document.getElementById('penulis').value = buku.penulis;
        document.getElementById('tahun_terbit').value = buku.tahun_terbit;

        document.getElementById('form-title').innerText = 'Ubah Data Buku';
        document.getElementById('btn-simpan').innerText = 'Perbarui Data';
        document.getElementById('btn-batal').style.display = 'inline-block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    async function deleteBuku(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus buku ini?')) {
            return;
        }

        try {
            const response = await fetch(`/api/buku/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Gagal menghapus data.');
            }

            showAlert('Buku berhasil dihapus!', 'success');
            window.location.reload();// loadBuku();

        } catch (error) {
            showAlert(error.message, 'danger');
        }
    }

    function resetForm() {
        const idElem = document.getElementById('buku-id');
        if (idElem) idElem.value = '';
        const formElem = document.getElementById('form-buku');
        if (formElem) formElem.reset();
        const titleElem = document.getElementById('form-title');
        if (titleElem) titleElem.innerText = 'Tambah Buku Baru';
        const saveElem = document.getElementById('btn-simpan');
        if (saveElem) saveElem.innerText = 'Simpan Data';
        const cancelElem = document.getElementById('btn-batal');
        if (cancelElem) cancelElem.style.display = 'none';
    }

    function showAlert(message, type) {
        const container = document.getElementById('alert-container');
        container.innerHTML = `
            <div class="alert alert-${type}">
                <span>${message}</span>
            </div>
        `;
    }
</script>
@endsection
