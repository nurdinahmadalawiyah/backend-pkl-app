<?php

namespace App\Http\Controllers;

use App\Models\BiodataIndustri;
use App\Models\PengajuanPKL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BiodataIndustriController extends Controller
{
    public function index()
    {
        $biodata_industri = DB::table('biodata_industri')
            ->join('mahasiswa', 'biodata_industri.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
            ->select('biodata_industri.*', 'mahasiswa.nama', 'mahasiswa.nim')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Biodata Industri',
            'data' => $biodata_industri
        ], 200);
    }

    public function showByPembimbing()
    {
        $biodata_industri = DB::table('biodata_industri')
            ->join('mahasiswa', 'biodata_industri.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
            ->select('biodata_industri.*', 'mahasiswa.nama', 'mahasiswa.nim')
            ->get();

        if (is_null($biodata_industri)) {
            return response()->json(['error' => 'Data Tidak Ditemukan.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Biodata Industri',
            'data' => $biodata_industri
        ], 200);
    }

    public function showByProdi()
    {
        $id_prodi = auth()->user()->id_prodi;

        $biodata_industri = DB::table('biodata_industri')
            ->join('mahasiswa', 'biodata_industri.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select('biodata_industri.id_biodata_industri' ,'mahasiswa.nama', 'mahasiswa.nim', 'prodi.nama_prodi', 'mahasiswa.prodi')
            ->where('mahasiswa.prodi', $id_prodi)
            ->get();

        if (is_null($biodata_industri)) {
            return response()->json(['error' => 'Data Tidak Ditemukan.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Biodata Industri Mahasiswa ' . auth()->user()->nama_prodi,
            'data' => $biodata_industri
        ], 200);
    }

    public function detailByProdi($id)
    {
        $biodata_industri = DB::table('biodata_industri')
            ->join('mahasiswa', 'biodata_industri.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
            ->select('biodata_industri.*', 'mahasiswa.nama', 'mahasiswa.nim')
            ->where('biodata_industri.id_biodata_industri', '=', $id)
            ->first();

        if (is_null($biodata_industri)) {
            return response()->json(['error' => 'Data Tidak Ditemukan.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Biodata Industri',
            'data' => $biodata_industri
        ], 200);
    }

    public function detailByPembimbing($id)
    {
        $biodata_industri = DB::table('biodata_industri')
            ->join('mahasiswa', 'biodata_industri.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
            ->select('biodata_industri.*', 'mahasiswa.nama', 'mahasiswa.nim')
            ->where('biodata_industri.id_biodata_industri', '=', $id)
            ->first();

        if (is_null($biodata_industri)) {
            return response()->json(['error' => 'Data Tidak Ditemukan.'], 404);
        }

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

    public function detailByUser()
    {

        $biodata_industri = BiodataIndustri::where('id_mahasiswa', Auth::user()->id_mahasiswa)->first();

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
            return response()->json($validator->errors(), 400);
        }

        // Cek apakah pengajuan mahasiswa sudah disetujui
        $pengajuan_pkl = PengajuanPKL::where('id_mahasiswa', $request->user()->id_mahasiswa)
            ->where('status', 'disetujui')
            ->first();

        if (!$pengajuan_pkl) {
            return response()->json(['message' => 'Pengajuan pkl belum disetujui'], 401);
        }

        // Cek apakah mahasiswa sudah memiliki biodata industri
        $biodata_industri = BiodataIndustri::where('id_mahasiswa', Auth::user()->id_mahasiswa)->first();

        if ($biodata_industri) {
            // Jika sudah ada, maka update data biodata industri yang sudah ada
            $biodata_industri->update([
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
        } else {
            // Jika belum ada, maka buat data biodata industri baru
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
        }

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
            return response()->json(['message' => 'Pengajuan PKL belum disetujui'], 401);
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
