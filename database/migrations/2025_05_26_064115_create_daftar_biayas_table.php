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
        Schema::create('daftar_biayas', function (Blueprint $table) {
            $table->id('id_daftar_biaya'); // Primary key
            $table->unsignedBigInteger('kategori_biaya_id');
            $table->double('nominal'); // Double with precision
            $table->timestamps(); // created_at & updated_at

            $table->foreign('kategori_biaya_id')->references('id_kategori_biaya')->on('kategori_biayas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_biayas');
    }
};
