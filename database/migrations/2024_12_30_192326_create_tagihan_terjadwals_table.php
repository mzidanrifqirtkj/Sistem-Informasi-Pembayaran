<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tagihan_terjadwals', function (Blueprint $table) {
            $table->id('id_tagihan_terjadwal');
            $table->foreignId('santri_id');
            $table->foreignId('biaya_terjadwal_id');
            $table->json('rincian');
            $table->double('nominal');
            $table->year('tahun');
            $table->enum('status', ['lunas', 'belum_lunas'])->default('belum_lunas');
            $table->timestamps();

            $table->foreign('santri_id')->references('id_santri')->on('santris')->onDelete('cascade');
            $table->foreign('biaya_terjadwal_id')->references('id_biaya_terjadwal')->on('biaya_terjadwals')->onDelete('cascade');
            //foreign key
        });
    }

    public function down()
    {
        Schema::dropIfExists('tagihans');
    }
};
