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
            $table->unsignedBigInteger('qori_kelas_id');
            $table->enum('semester', ['Ganjil', 'Genap']);

            $table->float('nilai')->nullable();
            $table->foreign('santri_id')->references('id_santri')->on('santris')->onDelete('cascade');
            $table->foreign('qori_kelas_id')->references('id_qori_kelas')->on('qori_kelas')->onDelete('cascade');
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
