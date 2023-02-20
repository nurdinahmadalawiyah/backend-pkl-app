<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanMagang extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_magang';
    protected $primaryKey = 'id_pengajuan';

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    protected $fillable = [
        'id_mahasiswa',
        'nama_perusahaan',
        'alamat_perusahaan',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];
}
