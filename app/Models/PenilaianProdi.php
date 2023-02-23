<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianProdi extends Model
{
    use HasFactory;

    protected $table = 'penilaian_prodi';
    protected $primaryKey = 'id_penilaian_prodi';

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id_mahasiswa');
    }

    protected $fillable = ['presentasi', 'dokumen', 'total_nilai', 'id_mahasiswa'];
}
