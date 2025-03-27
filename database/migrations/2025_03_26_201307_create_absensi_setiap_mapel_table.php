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
        Schema::create('absensi_setiap_mapel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('absensi_harian_id');
            $table->foreign('absensi_harian_id')->references('id')->on('absensi_harian')->onDelete('cascade');
            $table->unsignedBigInteger('mapel_kelas_id');
            $table->foreign('mapel_kelas_id')->references('id_mapel_kelas')->on('mapel_kelas')->onDelete('cascade');
            $table->unsignedBigInteger('santri_id');
            $table->foreign('santri_id')->references('id_santri')->on('santris')->onDelete('cascade');
            $table->enum('status', ['hadir', 'alpa', 'izin', 'sakit']);
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_setiap_mapel');
    }
};
