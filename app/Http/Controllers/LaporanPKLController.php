<?php

namespace App\Http\Controllers;

use App\Http\Resources\LaporanPKLResource;
use App\Models\LaporanPKL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LaporanPKLController extends Controller
{
    public function index()
    {
        $laporan_pkl = DB::table('laporan_pkl')
            ->join('mahasiswa', 'laporan_pkl.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select('laporan_pkl.id_laporan', 'mahasiswa.nama', 'mahasiswa.nim', 'prodi.nama_prodi', 'laporan_pkl.file', 'laporan_pkl.tanggal_upload')
            ->get();

        $laporan_pkl_resource = $laporan_pkl->map(function ($laporan) {
            return new LaporanPKLResource($laporan);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Semua Data Laporan PKL',
            'data' => $laporan_pkl_resource
        ], 200);
    }

    public function uploadLaporan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:doc,docx,pdf'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $file = $request->file('file');
        $destinationPath = "public\laporan";
        $filename = 'laporan_' . date("Ymd_his") . '_' . Auth::user()->nim . '.' . $file->extension();
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
