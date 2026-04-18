<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pesertabeasiswa', function (Blueprint $table) {
            // Kita tambahkan kolom plant_id setelah kolom nama_orang_tua
            // nullable() digunakan agar data yang sudah ada tidak error saat migrasi dijalankan
            $table->foreignId('plant_id')->after('nama_orang_tua')->nullable()->constrained('plants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesertabeasiswa', function (Blueprint $table) {
            // Menghapus constraint foreign key dan kolomnya
            $table->dropForeign(['plant_id']);
            $table->dropColumn('plant_id');
        });
    }
};
