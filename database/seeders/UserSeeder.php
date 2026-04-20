<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data user lama kalau ada (opsional tapi aman)
        User::where('username', 'admin')->delete();

        User::create([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
        ]);
    }
}
