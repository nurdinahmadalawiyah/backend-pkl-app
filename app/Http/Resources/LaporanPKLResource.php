<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LaporanPKLResource extends JsonResource
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
            'id_laporan' => $this->id_laporan,
            'nama' => $this->nama,
            'nim' => $this->nim,
            'nama_prodi' => $this->nama_prodi,
            'file' => asset('/storage/laporan/' . $this->file),
            'tanggal_upload' => $this->tanggal_upload,
        ];
    }
}
