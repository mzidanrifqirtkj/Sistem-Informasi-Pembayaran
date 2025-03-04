<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsensisTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id('id_absensi');
            $table->string('nis', 50);
            $table->tinyInteger('jumlah_hadir')->unsigned()->default(0);
            $table->tinyInteger('jumlah_izin')->unsigned()->default(0);
            $table->tinyInteger('jumlah_sakit')->unsigned()->default(0);
            $table->tinyInteger('jumlah_alpha')->unsigned()->default(0);
            $table->enum('bulan', ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'])->default('Jan');
            $table->enum('minggu_per_bulan', ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4', 'Minggu 5'])->nullable();
            $table->foreignId('tahun_ajar_id')->constrained('tahun_ajars')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->timestamps();

            $table->foreign('nis')->references('nis')->on('santris')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('absensis');
    }
}
