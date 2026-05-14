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
    Schema::create('doorprize', function (Blueprint $table) {
        $table->id();
        $table->string('nama_hadiah');
        $table->string('foto_hadiah')->nullable();
        $table->integer('jumlah_hadiah')->default(1);
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doorprize');
    }
};
