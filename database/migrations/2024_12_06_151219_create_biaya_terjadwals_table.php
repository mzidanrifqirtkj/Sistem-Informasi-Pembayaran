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
        Schema::create('biaya_terjadwals', function (Blueprint $table) {
            $table->string('nama_biaya');
            $table->id('id_biaya_terjadwal');
            $table->enum('periode', ['tahunan', 'sekali']);
            $table->double('nominal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_tahunans');
    }
};
