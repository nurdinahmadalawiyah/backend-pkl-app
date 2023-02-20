<?php

namespace App\Http\Controllers;

use App\Models\PengajuanMagang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PengajuanMagangController extends Controller
{
    public function showAllByUser()
    {
        $mahasiswa = Auth::user();

        $filterDataMahasiswa = ([
            'nama' => $mahasiswa->nama,
            'nim' => $mahasiswa->nim,
            'prodi' => $mahasiswa->prodi,
        ]);

        $pengajuan = PengajuanMagang::where('id_mahasiswa', $mahasiswa->id_mahasiswa)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Status Pengajuan Magang',
            'user' => $filterDataMahasiswa,
            'data' => $pengajuan
        ], 200);
    }

    public function index() {
        $pengajuan_magang = PengajuanMagang::all();

        return response()->json([
            'status' => 'success',
            'message' => 'Semua Data Pengajuan Magang',
            'data' => $pengajuan_magang,
        ], 200);
    }

    public function show($id)
    {
        $pengajuan_magang = PengajuanMagang::find($id);
        if (is_null($pengajuan_magang)) {
            return response()->json(['error' => 'Data Tidak Ditemukan.'], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Detail Pengajuan Magang id '. $id,
            'data' => $pengajuan_magang,
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

        $pengajuan = PengajuanMagang::create([
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

    // public function getAllPengajuan()
    // {
    //     try {
    //         $data = PengajuanMagang::get();
    //         return sendSuccessResponse($data);
    //     } catch (QueryException $e) {
    //         return sendErrorResponse("Something went wrong!", $e->getMessage(), 500);
    //     }
    // }

    // public function store($data = [])
    // {
    //     try {
    //         PengajuanMagang::create($data);
    //         return sendSuccessResponse([], 'Pengajuan Berhasil Terkirim', 201);
    //     } catch (QueryException $e) {
    //         return sendErrorResponse("Something went wrong!", $e->getMessage(), 500);
    //     }
    // }

    // public function show($id)
    // {
    //     try {
    //         $data = PengajuanMagang::find($id);
    //         if ($data) {
    //             return sendSuccessResponse($data);
    //         } else {
    //             return sendErrorResponse([], 'Data Tidak Ditemukan', 404);
    //         }
    //     } catch (QueryException $e) {
    //         return sendErrorResponse("Something went wrong!", $e->getMessage(), 500);
    //     }
    // }

    // public function update($data = [], $id)
    // {
    //     try {
    //         $data = PengajuanMagang::find($id)->update($data);
    //         return sendSuccessResponse($data, 'Data Berhasil Diperbarui');
    //     } catch (QueryException $e) {
    //         return sendErrorResponse("Something went wrong!", $e->getMessage(), 500);
    //     }
    // }

    // public function delete($id)
    // {
    //     try {
    //         $pengajuan =  PengajuanMagang::find($id);
    //         if ($pengajuan) {
    //             $pengajuan->delete();
    //             return sendSuccessResponse([], 'Data Berhasil Dihapus', 200);
    //         }
    //     } catch (QueryException $e) {
    //         return sendErrorResponse("Something Went Wrong!", $e->getMessage(), 500);
    //     }
    // }
}
