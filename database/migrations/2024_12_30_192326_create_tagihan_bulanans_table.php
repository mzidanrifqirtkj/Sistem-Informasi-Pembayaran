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
            $table->foreignId('santri_id');
            $table->enum('bulan', ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']);
            $table->year('tahun');
            $table->double('nominal');
            $table->json('rincian');
            $table->enum('status', ['lunas', 'belum_lunas'])->default('belum_lunas');
            $table->timestamps();

            $table->foreign('santri_id')->references('id_santri')->on('santris')->onDelete('cascade');

            //foreign key
        });
    }

    public function down()
    {
        Schema::dropIfExists('tagihans');
    }
};
