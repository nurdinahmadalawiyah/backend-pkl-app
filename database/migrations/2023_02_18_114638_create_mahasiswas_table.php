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
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id('id_mahasiswa');
            $table->string('username', 30)->unique();
            $table->string('nama', 50);
            $table->string('nim', 30)->unique();
            $table->unsignedBigInteger('prodi');
            $table->year('tahun_masuk');
            $table->string('email', 50)->nullable();
            $table->string('nomor_hp', 20)->nullable();
            $table->string('password', 100);
            $table->string('notification_id', 255)->nullable();
            $table->timestamps();

            $table->foreign('prodi')->references('id_prodi')->on('prodi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mahasiswa');
    }
};
