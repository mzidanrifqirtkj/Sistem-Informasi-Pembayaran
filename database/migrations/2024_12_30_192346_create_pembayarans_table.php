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
            $table->unsignedBigInteger('tagihan_terjadwal_id')->nullable();
            $table->double('nominal_pembayaran');
            $table->dateTime('tanggal_pembayaran');
            $table->unsignedBigInteger('created_by_id');
            $table->timestamps();

            // $table->foreign('santri_id')->references('id_santri')->on('santris');
            $table->foreign('tagihan_bulanan_id')->references('id_tagihan_bulanan')->on('tagihan_bulanans')->onDelete('cascade');
            $table->foreign('tagihan_terjadwal_id')->references('id_tagihan_terjadwal')->on('tagihan_terjadwals')->onDelete('cascade');
            $table->foreign('created_by_id')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayaran');
    }
};
