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
        Schema::create('tempat_pkl', function (Blueprint $table) {
            $table->id('id_tempat_pkl');
            $table->unsignedBigInteger('id_pengajuan');
            $table->unsignedBigInteger('id_pembimbing')->nullable();
            $table->timestamps();

            $table->foreign('id_pembimbing')->references('id_pembimbing')->on('pembimbing');
            $table->foreign('id_pengajuan')->references('id_pengajuan')->on('pengajuan_pkl');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tempat_pkl');
    }
};
