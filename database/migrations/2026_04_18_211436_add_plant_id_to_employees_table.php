<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek dulu, kalau kolom plant_id belum ada, baru buat.
        if (!Schema::hasColumn('employees', 'plant_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->unsignedBigInteger('plant_id')->nullable()->after('employee_name');
            });
        }
    }

    public function down(): void
    {
        // Kosongkan saja agar tidak error saat rollback
    }
};
