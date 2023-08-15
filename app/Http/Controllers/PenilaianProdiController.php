<?php

namespace App\Http\Controllers;

use App\Models\PenilaianProdi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                'penilaian_pembimbing.id_tempat_pkl',
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
            ->where('mahasiswa.id_mahasiswa', '=', $id)
            ->first();

        if (is_null($penilaian)) {
            return response()->json(['error' => 'Data Tidak Ditemukan.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail Nilai Mahasiswa ',
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
                'id_tempat_pkl' => $request->id_tempat_pkl,
                'presentasi' => $request->presentasi,
                'dokumen' => $request->dokumen,
                'total_nilai' => $total_nilai,
            ]);
        } else {
            // Jika belum ada, maka buat data penilaian baru
            $penilaian = PenilaianProdi::create([
                'id_mahasiswa' => $request->id_mahasiswa,
                'id_tempat_pkl' => $request->id_tempat_pkl,
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
