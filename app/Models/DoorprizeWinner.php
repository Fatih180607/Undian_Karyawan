<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoorprizeWinner extends Model
{
    use HasFactory;
    protected $table = 'doorprize_winners';
    protected $guarded = [];
    
    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }
}
