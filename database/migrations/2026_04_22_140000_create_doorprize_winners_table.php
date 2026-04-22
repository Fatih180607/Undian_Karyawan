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
        Schema::create('doorprize_winners', function (Blueprint $table) {
            $table->id();
            $table->string('nama_hadiah');
            $table->string('nama_karyawan');
            $table->string('nomor_karyawan');
            $table->unsignedBigInteger('plant_id');
            $table->string('nama_plant');
            $table->string('foto_hadiah')->nullable();
            $table->timestamp('waktu_menang');
            $table->integer('nomor_undian');
            $table->timestamps();

            $table->foreign('plant_id')->references('id')->on('plants');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doorprize_winners');
    }
};
