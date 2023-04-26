<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarHadir extends Model
{
    use HasFactory;

    protected $table = 'daftar_hadir';
    protected $primaryKey = 'id_daftar_hadir';

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    protected $fillable = [
        'id_mahasiswa',
        'id_tempat_pkl',
        'hari_tanggal',
        'minggu',
        'tanda_tangan'
    ];
}
