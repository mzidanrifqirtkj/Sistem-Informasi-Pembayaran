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
        Schema::create('mata_pelajarans', function (Blueprint $table) {
            $table->id('id_mapel');
            $table->string('nama_mapel');
            $table->unsignedBigInteger('kategori_mapel_id')->nullable();
            $table->foreign('kategori_mapel_id')->references('id_kategori_mapel')->on('kategori_mapels')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mata_pelajarans');
    }
};
