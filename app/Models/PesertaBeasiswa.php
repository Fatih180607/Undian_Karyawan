<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaBeasiswa extends Model
{
    // Pastikan nama tabel benar
    protected $table = 'pesertabeasiswa';

    protected $fillable = [
        'kode_peserta',
        'nama_anak',
        'jenjang_sekolah',
        'npk_orang_tua',
        'nama_orang_tua',
        'plant_id', // Tambahkan ini
        'is_winner'
    ];

    // Relasi ke Model Plant
    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plant_id');
    }
}
