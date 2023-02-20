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
        Schema::create('tempat_magang', function (Blueprint $table) {
            $table->id('id_tempat_magang');
            $table->unsignedBigInteger('id_pengajuan');
            $table->unsignedBigInteger('id_pembimbing')->nullable();
            $table->string('konfirmasi_nama_pembimbing', 50);
            $table->string('konfirmasi_nik_pembimbing', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tempat_magang');
    }
};
