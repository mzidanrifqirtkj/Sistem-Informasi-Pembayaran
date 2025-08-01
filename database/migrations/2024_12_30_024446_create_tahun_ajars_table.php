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
        Schema::create('tahun_ajars', function (Blueprint $table) {
            $table->id('id_tahun_ajar');
            $table->string('tahun_ajar')->unique(); // Contoh: 2023/2024
            $table->date('start_date'); // Tanggal mulai tahun akademik
            $table->date('end_date'); // Tanggal berakhir tahun akademik
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('tidak_aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahun_ajars');
    }
};
