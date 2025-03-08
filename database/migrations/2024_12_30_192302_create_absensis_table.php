<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsensisTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id('id_absensi');
            $table->string('nis', 50);
            $table->unsignedBigInteger('kelas_id'); // Relasi ke tabel kelas
            $table->date('tanggal');
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpa'])->default('alpa');
            $table->unsignedBigInteger('tahun_ajar_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('kelas_id')->references('id_kelas')->on('kelas')->onDelete('cascade');
            $table->foreign('tahun_ajar_id')->references('id_tahun_ajar')->on('tahun_ajars')->onDelete('cascade');
            $table->foreign('nis')->references('nis')->on('santris')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('absensis');
    }
}
