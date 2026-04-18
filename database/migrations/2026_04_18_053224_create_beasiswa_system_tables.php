<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // Tabel Kategori (SD, SMP, SMA, KULIAH)
        Schema::create('KategoriBeasiswa', function (Blueprint $table) {
            $table->id();
            $table->string('kode_sekolah');
            $table->string('jenjang_sekolah'); // SD, SMP, SMA, KULIAH
            $table->integer('nominal');
            $table->integer('kuota')->default(0);
            $table->timestamps();
        });

        // Tabel Peserta Anak
        Schema::create('PesertaBeasiswa', function (Blueprint $table) {
            $table->id();
            $table->string('kode_peserta');
            $table->string('nama_anak');
            $table->string('jenjang_sekolah');
            $table->string('npk_orang_tua');
            $table->string('nama_orang_tua');
            $table->boolean('is_winner')->default(false);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('PesertaBeasiswa');
        Schema::dropIfExists('KategoriBeasiswa');
    }
};
