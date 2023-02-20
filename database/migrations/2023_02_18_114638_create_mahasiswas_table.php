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
            $table->string('nim', 30);
            $table->unsignedBigInteger('prodi');
            $table->enum('semester', ['1 (Satu)', '2 (Dua)', '3 (Tiga)', '4 (Empat)', '5 (Lima)', '6 (Enam)', '7 (Tujuh)', '8 (Delapan)', '9 (Sembilan)', '10 (Sepuluh)', '11 (Sebelas)', '12 (Dua Belas)', '13 (Tiga Belas)', '14 (Empat Belas)']);
            $table->string('email', 50)->nullable();
            $table->string('nomor_hp', 20)->nullable();
            $table->string('password', 100);
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
