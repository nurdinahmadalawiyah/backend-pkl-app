<?php

namespace App\Http\Controllers;

use App\Models\PenilaianProdi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PenilaianProdiController extends Controller
{
    public function index()
    {
        $penilaian = DB::table('penilaian_prodi')
            ->join('mahasiswa', 'penilaian_prodi.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select('penilaian_prodi.*', 'mahasiswa.nama', 'prodi.nama_prodi', 'mahasiswa.nim')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Penilaian dari Prodi',
            'data' => $penilaian
        ], 200);
    }

    public function show($id)
    {
        $penilaian = DB::table('penilaian_prodi')
            ->leftJoin('penilaian_pembimbing', 'penilaian_prodi.id_mahasiswa', '=', 'penilaian_pembimbing.id_mahasiswa')
            ->join('mahasiswa', 'penilaian_prodi.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select(
                'mahasiswa.nama', 
                'prodi.nama_prodi', 
                'mahasiswa.nim',
                'penilaian_prodi.id_penilaian_prodi', 
                'penilaian_prodi.presentasi',
                'penilaian_prodi.dokumen', 
                'penilaian_pembimbing.*', 
                DB::raw('(penilaian_prodi.total_nilai + penilaian_pembimbing.total_nilai) / 2 AS nilai_akhir'),
                    DB::raw('CASE 
                    WHEN ((penilaian_prodi.total_nilai + penilaian_pembimbing.total_nilai) / 2) >= 85 THEN "A" 
                    WHEN ((penilaian_prodi.total_nilai + penilaian_pembimbing.total_nilai) / 2) >= 80 AND ((penilaian_prodi.total_nilai + penilaian_pembimbing.total_nilai) / 2) < 85 THEN "AB" 
                    WHEN ((penilaian_prodi.total_nilai + penilaian_pembimbing.total_nilai) / 2) >= 75 AND ((penilaian_prodi.total_nilai + penilaian_pembimbing.total_nilai) / 2) < 80 THEN "B" 
                    WHEN ((penilaian_prodi.total_nilai + penilaian_pembimbing.total_nilai) / 2) >= 70 AND ((penilaian_prodi.total_nilai + penilaian_pembimbing.total_nilai) / 2) < 75 THEN "BC" 
                    WHEN ((penilaian_prodi.total_nilai + penilaian_pembimbing.total_nilai) / 2) >= 60 AND ((penilaian_prodi.total_nilai + penilaian_pembimbing.total_nilai) / 2) < 70 THEN "D" 
                    WHEN ((penilaian_prodi.total_nilai + penilaian_pembimbing.total_nilai) / 2) >= 50 AND ((penilaian_prodi.total_nilai + penilaian_pembimbing.total_nilai) / 2) < 60 THEN "CD" 
                    WHEN ((penilaian_prodi.total_nilai + penilaian_pembimbing.total_nilai) / 2) >= 40 AND ((penilaian_prodi.total_nilai + penilaian_pembimbing.total_nilai) / 2) < 50 THEN "D" 
                    ELSE "E" 
                END AS nilai_huruf')
                )
            ->where('penilaian_prodi.id_penilaian_prodi', '=', $id)
            ->first();
        
        if (is_null($penilaian)) {
            return response()->json(['error' => 'Data Tidak Ditemukan.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail Nilai Mahasiswa',
            'data' => $penilaian,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_mahasiswa' => 'required|exists:mahasiswa,id_mahasiswa',
            'presentasi' => 'required|numeric|min:0|max:100',
            'dokumen' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $total_nilai = min(($request->presentasi + $request->dokumen) / 2, 100);

        // Cek apakah mahasiswa sudah memiliki data penilaian
        $penilaian = PenilaianProdi::where('id_mahasiswa', $request->id_mahasiswa)->first();

        if ($penilaian) {
            // Jika sudah ada, maka update data penilaian yang sudah ada
            $penilaian->update([
                'presentasi' => $request->presentasi,
                'dokumen' => $request->dokumen,
                'total_nilai' => $total_nilai,
            ]);
        } else {
            // Jika belum ada, maka buat data penilaian baru
            $penilaian = PenilaianProdi::create([
                'id_mahasiswa' => $request->id_mahasiswa,
                'presentasi' => $request->presentasi,
                'dokumen' => $request->dokumen,
                'total_nilai' => $total_nilai,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Penilaian dari Prodi',
            'data' => $penilaian
        ], 200);
    }

    public function destroy($id)
    {
        $penilaian = PenilaianProdi::findOrFail($id);
        $penilaian->delete();

        if($penilaian != null) {
            return response()->json([
                'message' => 'Nilai Dihapus',
                'data' => $penilaian
            ], 200);
        } else {
            return response()->json([
                'message' => 'Nilai Gagal Dihapus',
            ], 404);
        } 
    }
}
