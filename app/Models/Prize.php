<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    use HasFactory;

    // Menentukan nama tabel (karena kita pakai Bahasa Inggris di migration 'prizes')
    protected $table = 'prizes';

    // Daftar kolom yang boleh diisi secara massal (Mass Assignment)
    // Gunakan nama kolom Bahasa Indonesia sesuai permintaanmu tadi
    protected $fillable = [
        'nama_hadiah',
        'stok',
        'foto_hadiah',
    ];
}