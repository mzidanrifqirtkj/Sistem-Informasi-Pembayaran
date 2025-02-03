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
        Schema::create('wali_kelas', function (Blueprint $table) {
            $table->id('id_wali_kelas');
            $table->unsignedBigInteger('ustadz_id');
            $table->foreign('ustadz_id')->references('id_santri')->on('santris')->onDelete('cascade');
            $table->unsignedBigInteger('kelas_id');
            $table->foreign('kelas_id')->references('id_kelas')->on('kelas')->onDelete('cascade');
            $table->unsignedBigInteger('tahun_ajar_id');
            $table->foreign('tahun_ajar_id')->references('id_tahun_ajar')->on('tahun_ajars')->onDelete('cascade');
            $table->unique(['tahun_ajar_id', 'kelas_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wali_kelas');
    }
};
