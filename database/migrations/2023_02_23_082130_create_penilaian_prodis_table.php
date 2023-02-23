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
        Schema::create('penilaian_prodi', function (Blueprint $table) {
            $table->id('id_penilaian_prodi');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->float('presentasi');
            $table->float('dokumen');
            $table->float('total_nilai');
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
        Schema::dropIfExists('penilaian_prodi');
    }
};
