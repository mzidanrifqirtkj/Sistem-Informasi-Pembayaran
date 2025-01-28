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
            $table->unsignedBigInteger('kelas_id');
            $table->unsignedBigInteger('tahun_ajar_id');
            
            $table->foreign('santri_id')->references('id_santri')->on('santris')->onDelete('cascade');
            $table->foreign('kelas_id')->references('id_kelas')->on('kelas')->onDelete('cascade');
            $table->foreign('tahun_ajar_id')->references('id_tahun_ajar')->on('tahun_ajars')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('riwayat_kelas');
    }
}
