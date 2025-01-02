<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('nilais', function (Blueprint $table) {
            $table->id('id_nilai');
            $table->unsignedBigInteger('santri_id');
            $table->unsignedBigInteger('riwayat_kelas_id');
            $table->unsignedBigInteger('mata_pelajaran_id');

            $table->foreign('santri_id')->references('id_santri')->on('santris')->onDelete('cascade');
            $table->foreign('riwayat_kelas_id')->references('id_riwayat_kelas')->on('riwayat_kelas')->onDelete('cascade');
            $table->foreign('mata_pelajaran_id')->references('id_mata_pelajaran')->on('mata_pelajarans')->onDelete('cascade');

            $table->float('nilai_angka');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nilai');
    }
};
