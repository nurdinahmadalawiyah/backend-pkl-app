<?php

namespace App\Http\Controllers;

use App\Http\Resources\LaporanPKLResource;
use App\Models\LaporanPKL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LaporanPKLController extends Controller
{
    public function index() {
        $laporan_pkl = LaporanPKL::all();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Semua Data Laporan PKL',
            'data' => new LaporanPKLResource($laporan_pkl)
        ], 200);
    }

    public function uploadLaporan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:doc,docx,pdf|max:4096'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $file = $request->file('file');
        $destinationPath = "public\laporan";
        $filename = 'laporan_' . date("Ymd_his") . '_'. Auth::user()->nim . '.' . $file->extension();
        $laporan_pkl = LaporanPKL::create([
            'id_mahasiswa' => Auth::id(),
            'file' => $filename,
            'tanggal_upload' => now(),
        ]);
        Storage::putFileAs($destinationPath, $file, $filename);

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan berhasil diunggah',
            'data' => new LaporanPKLResource($laporan_pkl)
        ], 200);
    }

    public function cancel($id)
    {
        $laporan_pkl = LaporanPKL::findOrFail($id);
        Storage::delete('public/laporan/' . $laporan_pkl->file);
        $laporan_pkl->delete();

        if ($laporan_pkl != null) {
            return response()->json([
                'message' => 'Laporan dibatalkan',
                'data' => $laporan_pkl
            ]);
        } else {
            return response()->json([
                'message' => 'laporan_pkl PKL Tidak Ditemukan',
            ], 404);
        }
    }
}
