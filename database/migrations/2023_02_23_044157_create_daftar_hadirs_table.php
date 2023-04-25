<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daftar_hadir', function (Blueprint $table) {
            $table->id('id_daftar_hadir');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->unsignedBigInteger('id_tempat_pkl');
            $table->date('hari_tanggal');
            $table->unsignedInteger('minggu');
            $table->string('tanda_tangan');
            $table->timestamps();

            $table->foreign('id_mahasiswa')->references('id_mahasiswa')->on('mahasiswa');
            $table->foreign('id_tempat_pkl')->references('id_tempat_pkl')->on('tempat_pkl');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daftar_hadir');
    }
};
