<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalKegiatan extends Model
{
    use HasFactory;

    protected $table = 'jurnal_kegiatan';
    protected $primaryKey = 'id_jurnal_kegiatan';

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    protected $fillable = [
        'id_mahasiswa',
        'id_tempat_pkl',
        'tanggal',
        'minggu',
        'bidang_pekerjaan',
        'keterangan'
    ];
}
