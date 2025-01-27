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
        Schema::create('penilaian_santris', function (Blueprint $table) {
            $table->id('id_penilaian');
            $table->unsignedBigInteger('santri_id');
            $table->unsignedBigInteger('penugasan_id');

            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->float('nilai_tugas')->nullable();
            $table->float('nilai_uh')->nullable();
            $table->float('nilai_uts')->nullable();
            $table->float('nilai_uas')->nullable();
            $table->float('nilai_akhir')->nullable();

            $table->foreign('santri_id')->references('id_santri')->on('santris')->onDelete('cascade');
            $table->foreign('penugasan_id')->references('id_penugasan')->on('penugasan_ustadzs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_santris');
    }
};
