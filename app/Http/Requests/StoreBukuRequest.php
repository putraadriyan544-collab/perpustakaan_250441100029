<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBukuRequest extends FormRequest
{
    /**
     * Authorize request.
     */
    public function authorize(): bool
    {
        return true; // Soal 2e: mengembalikan nilai true
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        $tahunSekarang = date('Y');

        return [
            'kategori_id' => 'required|exists:kategoris,id',
            'judul'       => 'required|string|min:3|max:150',
            'penulis'     => 'required|string|min:3|max:100',
            'tahun_terbit'=> "required|integer|between:1900,{$tahunSekarang}",
        ];
    }

    /**
     * Custom error messages dalam bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string'   => ':attribute harus berupa teks.',
            'min'      => ':attribute minimal :min karakter.',
            'max'      => ':attribute maksimal :max karakter.',
            'integer'  => ':attribute harus berupa angka.',
            'between'  => ':attribute harus antara :min sampai :max.',
            'exists'   => ':attribute yang dipilih tidak valid.',
        ];
    }

    /**
     * Nama atribut yang ramah pengguna.
     */
    public function attributes(): array
    {
        return [
            'kategori_id'  => 'kategori',
            'judul'        => 'judul buku',
            'penulis'      => 'penulis',
            'tahun_terbit' => 'tahun terbit',
        ];
    }
}