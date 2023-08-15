<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDF;

class PenilaianController extends Controller
{
    public function index()
    {
        Storage::deleteDirectory('public/lembar-penilaian/');

        $penilaian = DB::table('mahasiswa')
            ->leftJoin('penilaian_pembimbing', 'mahasiswa.id_mahasiswa', '=', 'penilaian_pembimbing.id_mahasiswa')
            ->leftJoin('penilaian_prodi', 'mahasiswa.id_mahasiswa', '=', 'penilaian_prodi.id_mahasiswa')
            ->join('tempat_pkl', 'penilaian_pembimbing.id_tempat_pkl', '=', 'tempat_pkl.id_tempat_pkl')
            ->join('pembimbing', 'tempat_pkl.id_pembimbing', '=', 'pembimbing.id_pembimbing')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select(
                'penilaian_pembimbing.id_mahasiswa',
                'mahasiswa.nama',
                'prodi.nama_prodi',
                'mahasiswa.nim',
                'pembimbing.nama as nama_pembimbing',
                'pembimbing.nik',
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
                    WHEN (nilai_akhir) IS NULL THEN NULL 
                    WHEN (nilai_akhir) >= 85 THEN "A" 
                    WHEN (nilai_akhir) >= 80 AND (nilai_akhir) < 85 THEN "AB" 
                    WHEN (nilai_akhir) >= 75 AND (nilai_akhir) < 80 THEN "B" 
                    WHEN (nilai_akhir) >= 70 AND (nilai_akhir) < 75 THEN "BC" 
                    WHEN (nilai_akhir) >= 60 AND (nilai_akhir) < 70 THEN "C" 
                    WHEN (nilai_akhir) >= 50 AND (nilai_akhir) < 60 THEN "CD" 
                    WHEN (nilai_akhir) >= 40 AND (nilai_akhir) < 50 THEN "D" 
                    ELSE "E" 
                END AS nilai_huruf')
            )
            ->where('mahasiswa.id_mahasiswa', '=', Auth::user()->id_mahasiswa)
            ->first();

        if (is_null($penilaian)) {
            return response()->json(['error' => 'Data Tidak Ditemukan.'], 404);
        }

        $pdf = PDF::loadView('pdf.penilaian', compact('penilaian'))
            ->setPaper('a4');

        $filename = 'lembar_penilaian_' . $penilaian->nim . '.pdf';

        Storage::put('public/lembar-penilaian/' . $filename, $pdf->output());

        $pdf_url = asset('/storage/lembar-penilaian/' . $filename);

        return response()->json([
            'status' => 'success',
            'message' => 'Detail Nilai Mahasiswa ' . Auth::user()->nama,
            'pdf_url' => $pdf_url,
            'data' => $penilaian,
        ], 200);
    }
}
