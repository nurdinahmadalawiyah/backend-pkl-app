<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DaftarHadirResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id_daftar_hadir' => $this->id_daftar_hadir,
            'id_mahasiswa' => $this->id_mahasiswa,
            'hari_tanggal' => $this->hari_tanggal,
            'minggu' => $this->minggu,
            'tanda_tangan' => asset('/storage/tanda-tangan/' . $this->tanda_tangan),
        ];
    }
}
