<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('santri_tambahan_bulanans', function (Blueprint $table) {
            $table->id('id_santri_tambahan_bulanan');
            $table->foreignId('santri_id');
            $table->foreignId('tambahan_bulanan_id');
            $table->integer('jumlah');
            $table->timestamps();

            $table->foreign('santri_id')->references('id_santri')->on('santris')->onDelete('cascade');
            $table->foreign('tambahan_bulanan_id')->references('id_tambahan_bulanan')->on('tambahan_bulanans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('santri_tambahan_bulanans');
    }
};
