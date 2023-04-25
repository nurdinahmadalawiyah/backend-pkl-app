<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianPembimbing extends Model
{
    use HasFactory;

    protected $table = 'penilaian_pembimbing';
    protected $primaryKey = 'id_penilaian_pembimbing';

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id_mahasiswa');
    }

    protected $fillable = [
        'id_mahasiswa',
        'id_tempat_pkl',
        'integritas',
        'profesionalitas',
        'bahasa_inggris',
        'teknologi_informasi',
        'komunikasi',
        'kerja_sama',
        'organisasi',
        'total_nilai',
    ];
}
