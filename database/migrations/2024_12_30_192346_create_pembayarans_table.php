<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->unsignedBigInteger('tagihan_bulanan_id')->nullable();
            $table->unsignedBigInteger('tagihan_tahunan_id')->nullable();
            $table->unsignedBigInteger('santri_id');

            $table->foreign('tagihan_bulanan_id')->references('id_tagihan_bulanan')->on('tagihan_bulanans')->onDelete('set null');
            $table->foreign('tagihan_tahunan_id')->references('id_tagihan_tahunan')->on('tagihan_tahunans')->onDelete('set null');
            $table->foreign('santri_id')->references('id_santri')->on('santris')->onDelete('cascade');

            $table->date('tanggal_pembayaran');
            $table->integer('jumlah_dibayar');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayaran');
    }
};
