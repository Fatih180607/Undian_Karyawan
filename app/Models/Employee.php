<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    // Ini agar Laravel izinkan input data ke kolom-kolom ini
    protected $fillable = ['employee_number', 'employee_name', 'is_winner', 'prize_won'];
}