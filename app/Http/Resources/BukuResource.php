<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BukuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'kategori_id'  => $this->kategori_id,
            // Pemetaan: Ambil nama kategori dari relasi (atau ID jika tanpa relasi) menjadi atribut 'kategori'
            'kategori'     => $this->kategori->nama_kategori ?? $this->kategori_id,
            // Pemetaan: Kolom database 'judul' disajikan sebagai atribut 'judul'
            'judul'        => $this->judul,
            'penulis'      => $this->penulis,
            'tahun_terbit' => $this->tahun_terbit,
        ];
    }
}