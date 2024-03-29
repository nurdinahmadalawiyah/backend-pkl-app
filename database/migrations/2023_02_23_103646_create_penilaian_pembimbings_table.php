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
        Schema::create('penilaian_pembimbing', function (Blueprint $table) {
            $table->id('id_penilaian_pembimbing');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->unsignedBigInteger('id_tempat_pkl');
            $table->float('integritas');
            $table->float('profesionalitas');
            $table->float('bahasa_inggris');
            $table->float('teknologi_informasi');
            $table->float('komunikasi');
            $table->float('kerja_sama');
            $table->float('organisasi');
            $table->float('total_nilai');
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
        Schema::dropIfExists('penilaian_pembimbing');
    }
};
