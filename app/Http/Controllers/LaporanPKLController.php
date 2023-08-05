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
    public function indexByMahasiswa()
    {
        $laporan_pkl = DB::table('laporan_pkl')
            ->join('mahasiswa', 'laporan_pkl.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
            ->where('mahasiswa.id_mahasiswa', '=', Auth::user()->id_mahasiswa)
            ->select('laporan_pkl.id_laporan', 'mahasiswa.nama', 'mahasiswa.nim', 'laporan_pkl.file', 'laporan_pkl.tanggal_upload')
            ->first();

        if (is_null($laporan_pkl)) {
            return response()->json(['error' => 'Data Tidak Ditemukan.']);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Laporan PKL ' . Auth::user()->nama,
            'data' => [
                'id_laporan' => $laporan_pkl->id_laporan,
                'nama' => $laporan_pkl->nama,
                'nim' => $laporan_pkl->nim,
                'file' => asset('/storage/laporan/' . $laporan_pkl->file),
                'tanggal_upload' => $laporan_pkl->tanggal_upload,
            ]
        ], 200);
    }

    public function index()
    {
        $laporan_pkl = DB::table('laporan_pkl')
            ->join('mahasiswa', 'laporan_pkl.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select('laporan_pkl.id_laporan', 'mahasiswa.nama', 'mahasiswa.nim', 'prodi.nama_prodi', 'laporan_pkl.file', 'laporan_pkl.tanggal_upload')
            ->where('mahasiswa.prodi', '=', Auth::user()->id_prodi)
            ->get();

        $data = [];

        foreach ($laporan_pkl as $laporan) {
            $data[] = [
                'id_laporan' => $laporan->id_laporan,
                'nama' => $laporan->nama,
                'nim' => $laporan->nim,
                'nama_prodi' => $laporan->nama_prodi,
                'file' => asset('/storage/laporan/' . $laporan->file),
                'tanggal_upload' => $laporan->tanggal_upload,
            ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Semua Data Laporan PKL',
            'data' => $data
        ], 200);
    }

    public function detailByProdi($id_mahasiswa)
    {
        $laporan_pkl = DB::table('laporan_pkl')
            ->join('mahasiswa', 'laporan_pkl.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select('laporan_pkl.id_laporan', 'mahasiswa.id_mahasiswa', 'mahasiswa.nama', 'mahasiswa.nim', 'prodi.nama_prodi', 'laporan_pkl.file', 'laporan_pkl.tanggal_upload')
            ->where('mahasiswa.prodi', '=', Auth::user()->id_prodi)
            ->where('mahasiswa.id_mahasiswa', '=', $id_mahasiswa)
            ->first();
    
        if (is_null($laporan_pkl)) {
            return response()->json(['error' => 'Data Tidak Ditemukan.'], 404);
        }
    
        return response()->json([
            'status' => 'success',
            'message' => 'Detail Laporan PKL',
            'data' => [
                'id_laporan' => $laporan_pkl->id_laporan,
                'id_mahasiswa' => $laporan_pkl->id_mahasiswa,
                'nama' => $laporan_pkl->nama,
                'nim' => $laporan_pkl->nim,
                'nama_prodi' => $laporan_pkl->nama_prodi,
                'file' => asset('/storage/laporan/' . $laporan_pkl->file),
                'tanggal_upload' => $laporan_pkl->tanggal_upload,
            ]
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

        // Cek apakah mahasiswa sudah memiliki laporan
        $laporan_pkl = LaporanPKL::where('id_mahasiswa', Auth::user()->id_mahasiswa)->first();

        if ($laporan_pkl) {
            Storage::delete('public/laporan/' . $laporan_pkl->file);

            $file = $request->file('file');
            $destinationPath = "public\laporan";
            $filename = 'laporan_' . date("Ymd_his") . '_' . Auth::user()->nim . '.' . $file->extension();
            $laporan_pkl->update([
                'file' => $filename,
                'tanggal_upload' => now(),
            ]);
            Storage::putFileAs($destinationPath, $file, $filename);
        } else {
            $file = $request->file('file');
            $destinationPath = "public\laporan";
            $filename = 'laporan_' . date("Ymd_his") . '_' . Auth::user()->nim . '.' . $file->extension();
            $laporan_pkl = LaporanPKL::create([
                'id_mahasiswa' => Auth::id(),
                'file' => $filename,
                'tanggal_upload' => now(),
            ]);
            Storage::putFileAs($destinationPath, $file, $filename);
        }

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
