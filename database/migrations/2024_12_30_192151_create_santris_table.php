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
        Schema::create('santris', function (Blueprint $table) {
            //data pribadi
            $table->id('id_santri');
            $table->string('nama_santri', 100);
            $table->integer('nis')->unique();
            $table->string('nik')->unique();
            $table->string('no_kk');
            $table->string('jenis_kelamin')->enum("Laki-laki", "Perempuan");
            $table->date('tanggal_lahir');
            $table->string('tempat_lahir');
            $table->string('no_hp');
            $table->string('alamat');
            $table->string('golongan_darah');
            $table->string('pendidikan_formal');
            $table->string('pendidikan_non_formal');
            $table->string('foto')->nullable();
            $table->date('tanggal_masuk');
            //data lain santri
            $table->boolean('is_ustadz')->default(false);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('paket_pembayaran_id');

            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('paket_pembayaran_id')->references('id_paket_pembayaran')->on('paket_pembayarans')->onDelete('cascade');
            //data ayah
            $table->string('nama_ayah');
            $table->string('no_hp_ayah');
            $table->string('pekerjaan_ayah');
            $table->string('tempat_lahir_ayah');
            $table->date('tahun_lahir_ayah');
            $table->string('alamat_ayah');
            //data ibu
            $table->string('nama_ibu');
            $table->string('no_hp_ibu');
            $table->string('pekerjaan_ibu');
            $table->string('alamat_ibu');
            $table->string('tempat_lahir_ibu');
            $table->date('tahun_lahir_ibu');
            //data wali
            $table->string('nama_wali')->nullable();
            $table->string('no_hp_wali')->nullable();
            $table->string('pekerjaan_wali')->nullable();
            $table->string('alamat_wali')->nullable();
            $table->string('tempat_lahir_wali')->nullable();
            $table->date('tahun_lahir_wali')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('santris');
    }
};
