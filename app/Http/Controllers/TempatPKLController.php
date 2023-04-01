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
        ->select('tempat_pkl.id_tempat_pkl', 'tempat_pkl.id_pengajuan', 'mahasiswa.nama as nama_mahasiswa', 'prodi.nama_prodi', 'mahasiswa.nim', 'pembimbing.nama as nama_pembimbing', 'pembimbing.nik', 'tempat_pkl.konfirmasi_nama_pembimbing', 'tempat_pkl.konfirmasi_nik_pembimbing',)
        ->get();

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
