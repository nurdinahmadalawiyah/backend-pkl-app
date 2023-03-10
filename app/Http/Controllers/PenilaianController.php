<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenilaianController extends Controller
{
    public function index()
    {
        $penilaian = DB::table('penilaian_prodi')
            ->leftJoin('penilaian_pembimbing', 'penilaian_prodi.id_mahasiswa', '=', 'penilaian_pembimbing.id_mahasiswa')
            ->join('mahasiswa', 'penilaian_prodi.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select(
                'penilaian_prodi.id_mahasiswa', 
                'mahasiswa.nama', 
                'prodi.nama_prodi', 
                'mahasiswa.nim',
                'penilaian_prodi.id_penilaian_prodi', 
                'penilaian_pembimbing.id_penilaian_pembimbing', 
                'penilaian_prodi.presentasi',
                'penilaian_prodi.dokumen', 
                'penilaian_pembimbing.integritas', 
                'penilaian_pembimbing.profesionalitas', 
                'penilaian_pembimbing.bahasa_inggris', 
                'penilaian_pembimbing.teknologi_informasi', 
                'penilaian_pembimbing.komunikasi', 
                'penilaian_pembimbing.kerja_sama', 
                'penilaian_pembimbing.organisasi', 
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
            ->where('penilaian_prodi.id_penilaian_prodi', '=', Auth::user()->id_mahasiswa)
            ->first();
        
        if (is_null($penilaian)) {
            return response()->json(['error' => 'Data Tidak Ditemukan.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail Nilai Mahasiswa ' . Auth::user()->nama,
            'data' => $penilaian,
        ], 200);
    }
}