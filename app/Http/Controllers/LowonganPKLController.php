<?php

namespace App\Http\Controllers;

use App\Models\LowonganPKL;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LowonganPKLController extends Controller
{
    public function index()
    {
        $lowongan_pkl = LowonganPKL::all();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Semua Data Lowongan PKL',
            'data' => $lowongan_pkl
        ], 200);
    }

    public function showByProdi()
    {
        $prodi = Auth::user();
    
        if (!$prodi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data prodi tidak ditemukan'
            ], 404);
        }
    
        $lowongan_pkl = LowonganPKL::where('id_prodi', $prodi->id_prodi)->get();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Data Lowongan PKL',
            'data' => $lowongan_pkl
        ], 200);
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'posisi' => 'required',
            'nama_perusahaan' => 'required',
            'alamat_perusahaan' => 'required',
            'gambar' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $file = $request->file('gambar');
        $destinationPath = "public\images";
        $filename = 'lowongan_pkl_' . date("Ymd_his") . '.' . $file->extension();
        $lowongan_pkl = LowonganPKL::create([
            'id_prodi' => Auth::id(),
            'posisi' => $request->posisi,
            'nama_perusahaan' => $request->nama_perusahaan,
            'alamat_perusahaan' => $request->alamat_perusahaan,
            'gambar' => $filename,
            'url' => $request->url,
        ]);
        Storage::putFileAs($destinationPath, $file, $filename);

        return response()->json([
            'status' => 'success',
            'message' => 'Lowongan pkl Berhasil Disimpan',
            'data' => $lowongan_pkl
        ], 200);
    }

}
