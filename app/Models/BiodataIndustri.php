<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiodataIndustri extends Model
{
    use HasFactory;

    protected $table = 'biodata_industri';
    protected $primaryKey = 'id_biodata_industri';

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    protected $fillable = [
        'id_mahasiswa',
        'nama_industri',
        'nama_pimpinan',
        'alamat_kantor',
        'no_telp_fax',
        'contact_person',
        'bidang_usaha_jasa',
        'spesialisasi_produksi_jasa',
        'kapasitas_produksi',
        'jangkauan_pemasaran',
        'jumlah_tenaga_kerja_sd',
        'jumlah_tenaga_kerja_sltp',
        'jumlah_tenaga_kerja_slta',
        'jumlah_tenaga_kerja_smk',
        'jumlah_tenaga_kerja_sarjana_muda',
        'jumlah_tenaga_kerja_sarjana_magister',
        'jumlah_tenaga_kerja_sarjana_doktor',
    ];
}
