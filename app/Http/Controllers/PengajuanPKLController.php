<?php

namespace App\Http\Controllers;

use App\Models\PengajuanPKL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PengajuanPKLController extends Controller
{
    public function showAllByUser()
    {
        $mahasiswa = Auth::user();

        $prodi = DB::table('mahasiswa')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->where('mahasiswa.id_mahasiswa', '=', $mahasiswa->id_mahasiswa)
            ->select('mahasiswa.nama', 'prodi.nama_prodi', 'mahasiswa.nim')
            ->first();

        // $filterDataMahasiswa = ([
        //     'nama' => $mahasiswa->nama,
        //     'nim' => $mahasiswa->nim,
        //     'prodi' => $mahasiswa->prodi,
        // ]);

        $pengajuan = PengajuanPKL::where('id_mahasiswa', $mahasiswa->id_mahasiswa)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Status Pengajuan PKL',
            'user' => $prodi,
            'data' => $pengajuan
        ], 200);
    }

    public function index()
    {
        $pengajuan_pkl = DB::table('pengajuan_pkl')
            ->join('mahasiswa', 'pengajuan_pkl.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select('pengajuan_pkl.*', 'mahasiswa.nama', 'mahasiswa.nim', 'prodi.nama_prodi')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Semua Data Pengajuan PKL',
            'data' => $pengajuan_pkl,
        ], 200);
    }

    public function show($id)
    {
        // $pengajuan_pkl = PengajuanPKL::find($id);

        $pengajuan_pkl = DB::table('pengajuan_pkl')
            ->join('mahasiswa', 'pengajuan_pkl.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->where('pengajuan_pkl.id_pengajuan', '=', $id)
            ->select('pengajuan_pkl.*', 'mahasiswa.nama', 'mahasiswa.nim', 'prodi.nama_prodi')
            ->first();


        if (is_null($pengajuan_pkl)) {
            return response()->json(['error' => 'Data Tidak Ditemukan.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail Pengajuan PKL id ' . $id,
            'data' => $pengajuan_pkl,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_perusahaan' => 'required',
            'alamat_perusahaan' => 'required',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $pengajuan = PengajuanPKL::create([
            'id_mahasiswa' => Auth::id(),
            'nama_perusahaan' => $request->nama_perusahaan,
            'alamat_perusahaan' => $request->alamat_perusahaan,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status' => 'menunggu'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengajuan Berhasil Terkirim',
            'data' => $pengajuan
        ], 200);
    }

    public function setujuiPengajuan($id)
    {
        $pengajuan_pkl = PengajuanPKL::findOrFail($id);
        $pengajuan_pkl->status = 'disetujui';
        $pengajuan_pkl->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Pengajuan pkl berhasil disetujui',
            'data' => $pengajuan_pkl,
        ]);
    }

    public function tolakPengajuan($id)
    {
        $pengajuan_pkl = PengajuanPKL::findOrFail($id);
        $pengajuan_pkl->status = 'ditolak';
        $pengajuan_pkl->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Pengajuan pkl berhasil ditolak',
            'data' => $pengajuan_pkl,
        ]);
    }
}
