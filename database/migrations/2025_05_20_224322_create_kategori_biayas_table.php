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
        Schema::create('kategori_biayas', function (Blueprint $table) {
            $table->id('id_kategori_biaya');
            $table->enum('nama_kategori', ['tahunan', 'eksidental', 'tambahan', 'jalur']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kategori_biayas');
    }
};
