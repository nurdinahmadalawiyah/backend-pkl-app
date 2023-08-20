<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanKhusus extends Model
{
    use HasFactory;

    protected $table = 'catatan_khusus';
    protected $primaryKey = 'id_catatan_khusus';

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    protected $fillable = [
        'id_mahasiswa',
        'id_tempat_pkl',
        'catatan',
    ];
}
