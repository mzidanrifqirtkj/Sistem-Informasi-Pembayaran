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
        Schema::create('penugasan_ustadzs', function (Blueprint $table) {
            $table->id('id_penugasan');
            $table->unsignedBigInteger('ustadz_id');
            $table->foreign('ustadz_id')->references('id_santri')->on('santris')->onDelete('cascade')->nullable();
            $table->unsignedBigInteger('tahun_ajar_id');
            $table->unsignedBigInteger('mapel_id');
            $table->unsignedBigInteger('kelas_id');
            $table->foreign('tahun_ajar_id')->references('id_tahun_ajar')->on('tahun_ajars')->onDelete('cascade');
            $table->foreign('mapel_id')->references('id_mapel')->on('mata_pelajarans')->onDelete('cascade');
            $table->foreign('kelas_id')->references('id_kelas')->on('kelas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penugasan_ustadzs');
    }
};
