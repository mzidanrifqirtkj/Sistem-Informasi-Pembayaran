<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('biaya_santris', function (Blueprint $table) {
            $table->id('id_biaya_santri');
            $table->unsignedBigInteger('santri_id');
            $table->unsignedBigInteger('daftar_biaya_id');
            $table->integer('jumlah');
            $table->timestamps();

            // Foreign keys
            $table->foreign('santri_id')->references('id_santri')->on('santris')->onDelete('cascade');
            $table->foreign('daftar_biaya_id')->references('id_daftar_biaya')->on('daftar_biayas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('biaya_santris');
    }
};
