<?php

namespace App\Http\Controllers;

use App\Models\BiodataIndustri;
use App\Models\PengajuanPKL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BiodataIndustriController extends Controller
{
    public function index()
    {
        $biodata_industri = BiodataIndustri::all();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Biodata Industri',
            'data' => $biodata_industri
        ], 200);
    }

    public function show($id)
    {
        $biodata_industri = BiodataIndustri::find($id);
        if (is_null($biodata_industri)) {
            return response()->json(['error' => 'Data Tidak Ditemukan.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail Biodata Industri',
            'data' => $biodata_industri,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_industri' => 'required',
            'nama_pimpinan' => 'required',
            'alamat_kantor' => 'required',
            'no_telp_fax' => 'required',
            'contact_person' => 'required',
            'bidang_usaha_jasa' => 'required',
            'spesialisasi_produksi_jasa' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $pengajuan_pkl = PengajuanPKL::where('id_mahasiswa', $request->user()->id_mahasiswa)
            ->where('status', 'disetujui')
            ->first();

        if (!$pengajuan_pkl) {
            return response()->json(['message' => 'Pengajuan pkl belum disetujui'], 401);
        }

        $biodata_industri = BiodataIndustri::create([
            'id_mahasiswa' => Auth::id(),
            'nama_industri' => $request->nama_industri,
            'nama_pimpinan' => $request->nama_pimpinan,
            'alamat_kantor' => $request->alamat_kantor,
            'no_telp_fax' => $request->no_telp_fax,
            'contact_person' => $request->contact_person,
            'bidang_usaha_jasa' => $request->bidang_usaha_jasa,
            'spesialisasi_produksi_jasa' => $request->spesialisasi_produksi_jasa,
            'kapasitas_produksi' => $request->kapasitas_produksi,
            'jangkauan_pemasaran' => $request->jangkauan_pemasaran,
            'jumlah_tenaga_kerja_sd' => $request->jumlah_tenaga_kerja_sd,
            'jumlah_tenaga_kerja_sltp' => $request->jumlah_tenaga_kerja_sltp,
            'jumlah_tenaga_kerja_slta' => $request->jumlah_tenaga_kerja_slta,
            'jumlah_tenaga_kerja_smk' => $request->jumlah_tenaga_kerja_smk,
            'jumlah_tenaga_kerja_sarjana_muda' => $request->jumlah_tenaga_kerja_sarjana_muda,
            'jumlah_tenaga_kerja_sarjana_magister' => $request->jumlah_tenaga_kerja_sarjana_magister,
            'jumlah_tenaga_kerja_sarjana_doktor' => $request->jumlah_tenaga_kerja_sarjana_doktor,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Biodata Industri Berhasil Disimpan',
            'data' => $biodata_industri
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $pengajuan_pkl = PengajuanPKL::where('id_mahasiswa', $request->user()->id_mahasiswa)
            ->where('status', 'disetujui')
            ->first();

        if (!$pengajuan_pkl) {
            return response()->json(['message' => 'Pengajuan pkl belum disetujui'], 401);
        }

        $biodata_industri = BiodataIndustri::findOrFail($id);

        $biodata_industri->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Biodata Industri Berhasil Diperbarui',
            'data' => $biodata_industri
        ], 200);
    }

    public function destroy($id)
    {
        $biodata_industri = BiodataIndustri::findOrFail($id);
        $biodata_industri->delete();

        if ($biodata_industri != null) {
            return response()->json([
                'message' => 'Biodata Industri Dihapus',
                'data' => $biodata_industri
            ], 200);
        } else {
            return response()->json([
                'message' => 'Biodata Industri',
            ], 404);
        }
    }
}
