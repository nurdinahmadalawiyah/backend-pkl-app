<?php

namespace App\Http\Controllers;

use App\Models\PengajuanPKL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PDF;

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

        $pengajuan = PengajuanPKL::where('id_mahasiswa', $mahasiswa->id_mahasiswa)->orderByDesc('created_at')->get();

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

        $data_surat = DB::table('mahasiswa')
            ->join('pengajuan_pkl', 'mahasiswa.id_mahasiswa', '=', 'pengajuan_pkl.id_mahasiswa')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select('mahasiswa.nama', 'mahasiswa.nim', 'prodi.nama_prodi', 'mahasiswa.semester', 'prodi.nama_ketua_prodi', 'prodi.nidn_ketua_prodi')
            ->where('pengajuan_pkl.id_pengajuan', '=', $id)
            ->first();

        $pdf = PDF::loadView('pdf.surat_pengantar_pkl', compact(['pengajuan_pkl', 'data_surat']))
            ->setPaper('a4');

        $filename = 'surat_pengantar_pkl_' . $data_surat->nim . '.pdf';
        Storage::put('public/surat-pengantar-pkl/' . $filename, $pdf->output());

        $pengajuan_pkl->surat = $filename;
        $pengajuan_pkl->status = 'disetujui';
        $pengajuan_pkl->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Pengajuan PKL berhasil disetujui',
            'data' => [
                'id_pengajuan' => $pengajuan_pkl->id_pengajuan,
                'id_mahasiswa' => $pengajuan_pkl->id_mahasiswa,
                'nama_perusahaan' => $pengajuan_pkl->nama_perusahaan,
                'alamat_perusahaan' => $pengajuan_pkl->alamat_perusahaan,
                'tanggal_mulai' => $pengajuan_pkl->tanggal_mulai,
                'tanggal_selesai' => $pengajuan_pkl->tanggal_selesai,
                'status' => $pengajuan_pkl->status,
                'surat' => asset('/storage/surat-pengantar-pkl/' . $pengajuan_pkl->surat),
                'created_at' => $pengajuan_pkl->created_at,
                'updated_at' => $pengajuan_pkl->updated_at
            ]
        ]);
    }

    public function tolakPengajuan($id)
    {
        $pengajuan_pkl = PengajuanPKL::findOrFail($id);

        $data_surat = DB::table('mahasiswa')
        ->join('pengajuan_pkl', 'mahasiswa.id_mahasiswa', '=', 'pengajuan_pkl.id_mahasiswa')
        ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
        ->select('mahasiswa.nama', 'mahasiswa.nim', 'prodi.nama_prodi', 'mahasiswa.semester', 'prodi.nama_ketua_prodi', 'prodi.nidn_ketua_prodi')
        ->where('pengajuan_pkl.id_pengajuan', '=', $id)
        ->first();

        $filename = 'surat_pengantar_pkl_' . $data_surat->nim . '.pdf';
        Storage::delete('public/surat-pengantar-pkl/' . $filename);

        $pengajuan_pkl->surat = null;
        $pengajuan_pkl->status = 'ditolak';
        $pengajuan_pkl->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Pengajuan pkl berhasil ditolak',
            'data' => $pengajuan_pkl,
        ]);
    }
}
