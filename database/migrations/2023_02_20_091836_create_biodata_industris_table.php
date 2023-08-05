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
        Schema::create('biodata_industri', function (Blueprint $table) {
            $table->id('id_biodata_industri');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->unsignedBigInteger('id_tempat_pkl');
            $table->string('nama_industri', 50);
            $table->string('nama_pimpinan', 50);
            $table->text('alamat_kantor');
            $table->string('no_telp_fax', 20);
            $table->string('contact_person', 50);
            $table->text('bidang_usaha_jasa');
            $table->string('spesialisasi_produksi_jasa', 50);
            $table->string('jangkauan_pemasaran', 50);
            $table->unsignedInteger('kapasitas_produksi')->nullable();
            $table->unsignedInteger('jumlah_tenaga_kerja_sd')->nullable();
            $table->unsignedInteger('jumlah_tenaga_kerja_sltp')->nullable();
            $table->unsignedInteger('jumlah_tenaga_kerja_slta')->nullable();
            $table->unsignedInteger('jumlah_tenaga_kerja_smk')->nullable();
            $table->unsignedInteger('jumlah_tenaga_kerja_smea')->nullable();
            $table->unsignedInteger('jumlah_tenaga_kerja_smkk')->nullable();
            $table->unsignedInteger('jumlah_tenaga_kerja_sarjana_muda')->nullable();
            $table->unsignedInteger('jumlah_tenaga_kerja_sarjana_magister')->nullable();
            $table->unsignedInteger('jumlah_tenaga_kerja_sarjana_doktor')->nullable();
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
        Schema::dropIfExists('biodata_industri');
    }
};
