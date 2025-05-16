<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mapel_kelas', function (Blueprint $table) {
            $table->id('id_mapel_kelas');
            $table->unsignedBigInteger('kelas_id');
            $table->foreign('kelas_id')->references('id_kelas')->on('kelas')->onDelete('cascade');
            $table->unsignedBigInteger('mapel_id');
            $table->foreign('mapel_id')->references('id_mapel')->on('mata_pelajarans')->onDelete('cascade');
            $table->unsignedBigInteger('tahun_ajar_id');
            $table->foreign('tahun_ajar_id')->references('id_tahun_ajar')->on('tahun_ajars')->onDelete('cascade');
            $table->unsignedBigInteger('qori_id');
            $table->foreign('qori_id')->references('id_qori_kelas')->on('qori_kelas')->onDelete('cascade');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->unique(['kelas_id', 'mapel_id', 'tahun_ajar_id', 'qori_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mapel_kelas');
    }
};
