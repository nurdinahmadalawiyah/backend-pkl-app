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
        Schema::create('lowongan_pkl', function (Blueprint $table) {
            $table->id('id_lowongan');
            $table->unsignedBigInteger('id_prodi');
            $table->string('posisi', 100);
            $table->string('nama_perusahaan', 100);
            $table->text('alamat_perusahaan');
            $table->string('gambar');
            $table->string('url')->nullable();
            $table->string('sumber');
            $table->timestamps();

            $table->foreign('id_prodi')->references('id_prodi')->on('prodi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lowongan_pkl');
    }
};
