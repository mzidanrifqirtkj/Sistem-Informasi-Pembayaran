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
            $table->enum('status', ['aktif', 'tidak aktif'])->default('aktif');
            $table->unsignedBigInteger('nis');
            $table->foreign('nis')->references('id_santri')->on('santris')->onDelete('cascade')->where('is_ustadz', true);
            $table->unique(['nis']);
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
