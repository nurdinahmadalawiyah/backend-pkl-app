<?php

namespace App\Http\Controllers;

use App\Models\TempatPKL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TempatPKLController extends Controller
{
    public function index() 
    {
        $tempat_pkl = TempatPKL::all();

        return response()->json([
            'status' => 'success',
            'message' => 'Semua Data Konfirmasi Mahasiswa Diterima PKL',
            'data' => $tempat_pkl,
        ], 200);
    }

    public function selectMentor(Request $request, $id)
    {
        $tempat_pkl = TempatPKL::findOrFail($id);
        $tempat_pkl->id_pembimbing = $request->id_pembimbing;
        $tempat_pkl->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil Memilih Pembimbing',
            'data' => $tempat_pkl,
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

        $tempat_pkl = TempatPKL::create([
            'id_pengajuan' => $request->id_pengajuan,
            'id_pembimbing' => $request->id_pembimbing,
            'konfirmasi_nama_pembimbing' => $request->konfirmasi_nama_pembimbing,
            'konfirmasi_nik_pembimbing' => $request->konfirmasi_nik_pembimbing
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil konfirmasi diterima pkl',
            'data' => $tempat_pkl,
        ], 200);
    }
}
