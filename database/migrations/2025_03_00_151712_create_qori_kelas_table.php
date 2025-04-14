<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('qori_kelas', function (Blueprint $table) {
            $table->id('id_qori_kelas');
            $table->unsignedBigInteger('ustadz_id');
            $table->unsignedBigInteger('mapel_kelas_id');
            $table->foreign('ustadz_id')->references('id_santri')->on('santris')->onDelete('cascade')->where('is_ustadz', true);
            $table->foreign('mapel_kelas_id')->references('id_mapel_kelas')->on('mapel_kelas')->onDelete('cascade');
            $table->unique(['ustadz_id', 'mapel_kelas_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qori_kelas');
    }
};
