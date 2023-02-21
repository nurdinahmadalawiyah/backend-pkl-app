<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LowonganPKL extends Model
{
    use HasFactory;

    protected $table = 'lowongan_pkl';
    protected $primaryKey = 'id_lowongan';

    protected $fillable = [
        'id_prodi',
        'posisi',
        'nama_perusahaan',
        'alamat_perusahaan',
        'gambar',
        'url',
    ];
}
