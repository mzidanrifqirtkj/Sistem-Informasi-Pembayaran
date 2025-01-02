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
        Schema::create('paket_pembayarans', function (Blueprint $table) {
            $table->id('id_paket_pembayaran');
            $table->string('nama_paket');
            $table->integer('nominal');
            $table->string('detail_pembayaran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paket__pembayarans');
    }
};
