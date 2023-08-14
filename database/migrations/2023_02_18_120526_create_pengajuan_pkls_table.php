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
        Schema::create('pengajuan_pkl', function (Blueprint $table) {
            $table->id('id_pengajuan');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->string('nama_perusahaan', 50);
            $table->text('alamat_perusahaan');
            $table->string('ditujukan', 100)->after('alamat_perusahaan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak']);
            $table->string('surat')->nullable();
            $table->timestamps();
            
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
        Schema::dropIfExists('pengajuan_pkl');
    }
};
