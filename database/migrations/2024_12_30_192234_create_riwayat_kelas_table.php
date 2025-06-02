<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiwayatKelasTable extends Migration
{
    public function up()
    {
        Schema::create('riwayat_kelas', function (Blueprint $table) {
            $table->id('id_riwayat_kelas');

            $table->unsignedBigInteger('santri_id');
            $table->unsignedBigInteger('mapel_kelas_id');

            $table->foreign('santri_id')->references('id_santri')->on('santris')->onDelete('cascade');
            $table->foreign('mapel_kelas_id')->references('id_mapel_kelas')->on('mapel_kelas')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('riwayat_kelas');
    }
}
