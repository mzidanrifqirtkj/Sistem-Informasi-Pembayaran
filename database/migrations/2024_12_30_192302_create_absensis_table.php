<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id('id_absensi');
            $table->unsignedBigInteger('riwayat_kelas_id');
            $table->unsignedBigInteger('santri_id');

            $table->foreign('riwayat_kelas_id')->references('id_riwayat_kelas')->on('riwayat_kelas')->onDelete('cascade');
            $table->foreign('santri_id')->references('id_santri')->on('santris')->onDelete('cascade');

            $table->date('tanggal_absen');
            $table->integer('jumlah_hadir');
            $table->integer('jumlah_izin');
            $table->integer('jumlah_sakit');
            $table->integer('jumlah_alpha');
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('absensi');
    }
};
