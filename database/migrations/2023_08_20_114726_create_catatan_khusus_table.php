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
        Schema::create('catatan_khusus', function (Blueprint $table) {
            $table->id('id_catatan_khusus');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->unsignedBigInteger('id_tempat_pkl');
            $table->text('catatan');
            $table->timestamps();

            $table->foreign('id_tempat_pkl')->references('id_tempat_pkl')->on('tempat_pkl');
            $table->foreign('id_mahasiswa')->references('id_mahasiswa')->on('mahasiswa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catatan_khusus');
    }
};
