<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeasiswaKuotaPlant extends Model
{
    protected $fillable = ['plant_id', 'kategori_id', 'jumlah_slot'];

    public function plant() {
        return $this->belongsTo(Plant::class);
    }

    public function kategori() {
        return $this->belongsTo(KategoriBeasiswa::class, 'kategori_id');
    }
}
