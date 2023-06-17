<?php

namespace App\Http\Controllers;

use App\Models\TempatPKL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TempatPKLController extends Controller
{
    public function index()
    {
        $tempat_pkl = DB::table('tempat_pkl')
        ->join('pengajuan_pkl', 'tempat_pkl.id_pengajuan', '=', 'pengajuan_pkl.id_pengajuan')
        ->join('mahasiswa', 'pengajuan_pkl.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
        ->leftJoin('pembimbing', 'tempat_pkl.id_pembimbing', '=', 'pembimbing.id_pembimbing')
        ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
        ->leftJoin('biodata_industri', 'tempat_pkl.id_tempat_pkl', '=', 'biodata_industri.id_biodata_industri')
        ->select('tempat_pkl.id_tempat_pkl', 'tempat_pkl.id_pengajuan', 'mahasiswa.nama as nama_mahasiswa', 'prodi.nama_prodi', 'mahasiswa.nim', 'biodata_industri.id_biodata_industri','pembimbing.nama as nama_pembimbing', 'pembimbing.nik', 'pengajuan_pkl.*')
        ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Semua Data Konfirmasi Mahasiswa Diterima PKL',
            'data' => $tempat_pkl,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_pengajuan' => 'required',
            'id_pembimbing' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $tempat_pkl = TempatPKL::create([
            'id_pengajuan' => $request->id_pengajuan,
            'id_pembimbing' => $request->id_pembimbing,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil konfirmasi diterima pkl',
            'data' => $tempat_pkl,
        ], 200);
    }
}
