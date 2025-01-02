<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tagihan_tahunans', function (Blueprint $table) {
            $table->id('id_tagihan_tahunan');
            $table->unsignedBigInteger('santri_id');
            $table->foreign('santri_id')->references('id_santri')->on('santris')->onDelete('cascade');

            $table->string('jenis_tagihan');
            $table->string('tahun');
            $table->integer('jumlah');
            $table->enum('status', ['Belum Dibayar', 'Lunas']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tagihan_tahunan');
    }
};
