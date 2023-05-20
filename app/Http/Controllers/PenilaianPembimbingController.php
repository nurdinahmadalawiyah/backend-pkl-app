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
        $id_pembimbing = auth()->user()->id_pembimbing;

        $penilaian = DB::table('tempat_pkl')
            ->join('pengajuan_pkl', 'tempat_pkl.id_pengajuan', '=', 'pengajuan_pkl.id_pengajuan')
            ->join('mahasiswa', 'pengajuan_pkl.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
            ->leftJoin('pembimbing', 'tempat_pkl.id_pembimbing', '=', 'pembimbing.id_pembimbing')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select('tempat_pkl.id_tempat_pkl', 'mahasiswa.id_mahasiswa', 'mahasiswa.nama as nama_mahasiswa', 'prodi.nama_prodi', 'mahasiswa.nim', 'pembimbing.nama as nama_pembimbing', 'pembimbing.nik',)
            ->where('tempat_pkl.id_pembimbing', $id_pembimbing)
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Penilaian dari Pembimbing',
            'data' => $penilaian
        ], 200);
    }

    public function show($id_mahasiswa)
    {
        $penilaian = DB::table('penilaian_pembimbing')
            ->where('id_mahasiswa', $id_mahasiswa)
            ->first();

        if (is_null($penilaian)) {
            return response()->json(['error' => 'Data Tidak Ditemukan.']);
        }

        // Konversi kolom numerik ke format desimal dengan 2 angka di belakang koma
        $penilaian->integritas = number_format($penilaian->integritas, 2);
        $penilaian->profesionalitas = number_format($penilaian->profesionalitas, 2);
        $penilaian->bahasa_inggris = number_format($penilaian->bahasa_inggris, 2);
        $penilaian->teknologi_informasi = number_format($penilaian->teknologi_informasi, 2);
        $penilaian->komunikasi = number_format($penilaian->komunikasi, 2);
        $penilaian->kerja_sama = number_format($penilaian->kerja_sama, 2);
        $penilaian->organisasi = number_format($penilaian->organisasi, 2);
        $penilaian->total_nilai = number_format($penilaian->total_nilai, 2);

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
            'id_tempat_pkl' => 'required',
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

        $total_nilai = min(($request->integritas +
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
                'id_tempat_pkl' => $request->id_tempat_pkl,
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
                'id_tempat_pkl' => $request->id_tempat_pkl,
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

        if ($penilaian != null) {
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
