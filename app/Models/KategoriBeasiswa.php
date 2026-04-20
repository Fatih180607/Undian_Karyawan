<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KategoriBeasiswa extends Model {
    protected $table = 'KategoriBeasiswa'; // Paksa pakai nama tabel ini
    protected $fillable = ['kode_sekolah', 'jenjang_sekolah', 'nominal', 'kuota', 'plant_id'];

    public function plant() {
        return $this->belongsTo(Plant::class);
    }
}
