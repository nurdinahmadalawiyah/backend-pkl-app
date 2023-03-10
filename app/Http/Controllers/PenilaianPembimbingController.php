<?php

namespace App\Http\Controllers;

use App\Models\PenilaianPembimbing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PenilaianPembimbingController extends Controller
{
    public function index()
    {
        $penilaian = DB::table('penilaian_pembimbing')
            ->join('mahasiswa', 'penilaian_pembimbing.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select('penilaian_pembimbing.*', 'mahasiswa.nama', 'prodi.nama_prodi', 'mahasiswa.nim')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Penilaian dari Pembimbing',
            'data' => $penilaian
        ], 200);
    }

    public function show($id)
    {
        $penilaian = PenilaianPembimbing::find($id);
        
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
            'integritas' => 'required|numeric|min:0|max:100',
            'profesionalitas' => 'required|numeric|min:0|max:100',
            'bahasa_inggris' => 'required|numeric|min:0|max:100',
            'teknologi_informasi' => 'required|numeric|min:0|max:100',
            'komunikasi' => 'required|numeric|min:0|max:100',
            'kerja_sama' => 'required|numeric|min:0|max:100',
            'organisasi' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $total_nilai = min((
            $request->integritas +
            $request->profesionalitas +
            $request->bahasa_inggris +
            $request->teknologi_informasi +
            $request->komunikasi +
            $request->kerja_sama +
            $request->organisasi
            ) / 7, 100);

        // Cek apakah mahasiswa sudah memiliki data penilaian
        $penilaian = PenilaianPembimbing::where('id_mahasiswa', $request->id_mahasiswa)->first();

        if ($penilaian) {
            // Jika sudah ada, maka update data penilaian yang sudah ada
            $penilaian->update([
                'integritas' => $request->integritas,
                'profesionalitas' => $request->profesionalitas,
                'bahasa_inggris' => $request->bahasa_inggris,
                'teknologi_informasi' => $request->teknologi_informasi,
                'komunikasi' => $request->komunikasi,
                'kerja_sama' => $request->kerja_sama,
                'organisasi' => $request->organisasi,
                'total_nilai' => $total_nilai,
            ]);
        } else {
            // Jika belum ada, maka buat data penilaian baru
            $penilaian = PenilaianPembimbing::create([
                'id_mahasiswa' => $request->id_mahasiswa,
                'integritas' => $request->integritas,
                'profesionalitas' => $request->profesionalitas,
                'bahasa_inggris' => $request->bahasa_inggris,
                'teknologi_informasi' => $request->teknologi_informasi,
                'komunikasi' => $request->komunikasi,
                'kerja_sama' => $request->kerja_sama,
                'organisasi' => $request->organisasi,
                'total_nilai' => $total_nilai,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Penilaian dari Pembimbing',
            'data' => $penilaian
        ], 200);
    }

    public function destroy($id)
    {
        $penilaian = PenilaianPembimbing::findOrFail($id);
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
