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
        Schema::table('beasiswa_kuota_plants', function (Blueprint $table) {
            if (!Schema::hasColumn('beasiswa_kuota_plants', 'plant_id')) {
                $table->unsignedBigInteger('plant_id')->after('id');
                $table->unsignedBigInteger('kategori_id')->after('plant_id');
                $table->integer('jumlah_slot')->default(0)->after('kategori_id');
                $table->foreign('plant_id')->references('id')->on('plants')->onDelete('cascade');
                $table->foreign('kategori_id')->references('id')->on('KategoriBeasiswa')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beasiswa_kuota_plants', function (Blueprint $table) {
            if (Schema::hasColumn('beasiswa_kuota_plants', 'plant_id')) {
                $table->dropForeign(['plant_id']);
                $table->dropForeign(['kategori_id']);
                $table->dropColumn(['plant_id', 'kategori_id', 'jumlah_slot']);
            }
        });
    }
};
