<?php

namespace App\Http\Controllers;

use App\Models\TempatMagang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TempatMagangController extends Controller
{
    public function index() 
    {
        $tempat_magang = TempatMagang::all();

        return response()->json([
            'status' => 'success',
            'message' => 'Semua Data Konfirmasi Mahasiswa Diterima Magang',
            'data' => $tempat_magang,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'konfirmasi_nama_pembimbing' => 'required',
            'konfirmasi_nik_pembimbing' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $tempat_magang = TempatMagang::create([
            'id_pengajuan' => $request->id_pengajuan,
            'id_pembimbing' => $request->id_pembimbing,
            'konfirmasi_nama_pembimbing' => $request->konfirmasi_nama_pembimbing,
            'konfirmasi_nik_pembimbing' => $request->konfirmasi_nik_pembimbing
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil konfirmasi diterima magang',
            'data' => $tempat_magang,
        ], 200);
    }
}
