<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tagihan_bulanans', function (Blueprint $table) {
            $table->id('id_tagihan_bulanan');
            $table->unsignedBigInteger('santri_id');
            $table->foreign('santri_id')->references('id_santri')->on('santris');

            $table->string('nama_tagihan')->default('Syahriyah');
            $table->enum('bulan', ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']);
            $table->year('tahun');
            $table->decimal('jumlah', 10, 2);
            $table->enum('status', ['Belum Dibayar', 'Lunas']);
            $table->timestamps();

            //foreign key
        });
    }

    public function down()
    {
        Schema::dropIfExists('tagihan_bulanan');
    }
};
