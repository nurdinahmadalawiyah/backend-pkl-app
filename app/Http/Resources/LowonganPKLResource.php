<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LowonganPKLResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $gambar = ($this->sumber == 'Politeknik TEDC Bandung') ? asset('/storage/images/' . $this->gambar) : $this->gambar;

        return [
            'id_lowongan' => $this->id_lowongan,
            'id_prodi' => $this->id_prodi,
            'posisi' => $this->posisi,
            'nama_perusahaan' => $this->nama_perusahaan,
            'alamat_perusahaan' => $this->alamat_perusahaan,
            'gambar' => $gambar,
            'url' => $this->url,
            'sumber' => $this->sumber,
        ];
    }
}
