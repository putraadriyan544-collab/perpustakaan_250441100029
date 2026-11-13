<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'bukus';
    protected $fillable = ['kategori_id', 'judul', 'penulis', 'tahun_terbit'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class); /*artinya relasi buku dengan table Kategori adalah 1 to many , 1 buku dimiliki banyak kategori */
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }
}