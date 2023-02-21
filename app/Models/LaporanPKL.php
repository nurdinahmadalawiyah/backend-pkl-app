<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanPKL extends Model
{
    use HasFactory;

    protected $table = 'laporan_pkl';
    protected $primaryKey = 'id_laporan';

    protected $fillable = [
        'id_laporan',
        'id_mahasiswa',
        'file',
        'tanggal_upload',
    ];
}
