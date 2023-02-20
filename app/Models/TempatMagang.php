<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempatMagang extends Model
{
    use HasFactory;

    protected $table = 'tempat_magang';
    protected $primaryKey = 'id_tempat_magang';

    protected $fillable = [
        'id_pengajuan',
        'id_pembimbing',
        'konfirmasi_nama_pembimbing',
        'konfirmasi_nik_pembimbing',
    ];
}
