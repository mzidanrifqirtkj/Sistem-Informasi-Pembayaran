<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsensisTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id('id_absensi');
            $table->unsignedBigInteger('santri_id');
            $table->tinyInteger('jumlah_hadir')->default(0);
            $table->tinyInteger('jumlah_izin')->default(0);
            $table->tinyInteger('jumlah_sakit')->default(0);
            $table->tinyInteger('jumlah_alpha')->default(0);
            $table->enum('bulan', [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec'
            ])->default('Jan');

            // Enum untuk minggu dalam bulan
            $table->enum('minggu_per_bulan', ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4', 'Minggu 5']);
            $table->unsignedBigInteger('tahun_ajar_id');
            $table->unsignedBigInteger('kelas_id');
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('santri_id')->references('id')->on('santris')->onDelete('cascade');
            $table->foreign('tahun_ajar_id')->references('id')->on('tahun_ajar')->onDelete('cascade');
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
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
