<?php

namespace App\Http\Controllers;

use App\Http\Resources\DaftarHadirResource;
use App\Models\DaftarHadir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PDF;

class DaftarHadirController extends Controller
{
    public function index(Request $request)
    {
        $daftar_hadir = DaftarHadir::where('id_mahasiswa', $request->user()->id_mahasiswa)
            ->orderBy('minggu')
            ->get();

        Storage::deleteDirectory('public/daftar-hadir/');

        $grouped = $daftar_hadir->groupBy('minggu')->map(function ($item) {
            return [
                'minggu' => $item[0]->minggu,
                'data_kehadiran' => $item->map(function ($subitem) {
                    return [
                        'id_daftar_hadir' => $subitem->id_daftar_hadir,
                        'id_mahasiswa' => $subitem->id_mahasiswa,
                        'hari_tanggal' => $subitem->hari_tanggal,
                        'minggu' => $subitem->minggu,
                        'tanda-tangan' => asset('/storage/tanda-tangan/' . $subitem->tanda_tangan),
                    ];
                }),
            ];
        });

        $data_kehadiran = DB::table('mahasiswa')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->join('pengajuan_pkl', 'mahasiswa.id_mahasiswa', '=', 'pengajuan_pkl.id_mahasiswa')
            ->join('tempat_pkl', 'pengajuan_pkl.id_pengajuan', '=', 'tempat_pkl.id_pengajuan')
            ->join('pembimbing', 'tempat_pkl.id_pembimbing', '=', 'pembimbing.id_pembimbing')
            ->select('mahasiswa.nama', 'mahasiswa.nim', 'prodi.nama_prodi', 'pembimbing.nama as nama_pembimbing', 'pembimbing.nik')
            ->where('mahasiswa.prodi', Auth::user()->id_mahasiswa)
            ->first();

        $pdf = PDF::loadView('pdf.daftar_hadir', ['grouped' => $grouped, 'data_kehadiran' => $data_kehadiran])->setPaper('a4');
        $filename = 'daftar_hadir_' . $data_kehadiran->nim . '.pdf';
        Storage::put('public/daftar-hadir/' . $filename, $pdf->output());
        $pdf_url = asset('storage/daftar-hadir/' . $filename);

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar Hadir '  . $request->user()->nama,
            'pdf_url' => $pdf_url,
            'data' => $grouped->values(),
        ], 200);
    }

    public function indexByPembimbing()
    {
        $id_pembimbing = auth()->user()->id_pembimbing;

        $daftar_hadir = DB::table('tempat_pkl')
            ->join('pengajuan_pkl', 'tempat_pkl.id_pengajuan', '=', 'pengajuan_pkl.id_pengajuan')
            ->join('mahasiswa', 'pengajuan_pkl.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
            ->leftJoin('pembimbing', 'tempat_pkl.id_pembimbing', '=', 'pembimbing.id_pembimbing')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select('tempat_pkl.id_tempat_pkl', 'mahasiswa.id_mahasiswa', 'mahasiswa.nama as nama_mahasiswa', 'prodi.nama_prodi', 'mahasiswa.nim', 'pembimbing.nama as nama_pembimbing', 'pembimbing.nik')
            ->where('tempat_pkl.id_pembimbing', $id_pembimbing)
            ->get();

        if (is_null($daftar_hadir)) {
            return response()->json(['error' => 'Data Tidak Ditemukan.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar Hadir',
            'data' => $daftar_hadir,
        ], 200);
    }

    public function indexByProdi()
    {
        $id_prodi = auth()->user()->id_prodi;

        $daftar_hadir = DB::table('mahasiswa')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select('mahasiswa.id_mahasiswa', 'mahasiswa.nama', 'mahasiswa.nim', 'prodi.nama_prodi', 'mahasiswa.prodi')
            ->where('mahasiswa.prodi', $id_prodi)
            ->get();

        if (is_null($daftar_hadir)) {
            return response()->json(['error' => 'Data Tidak Ditemukan.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar Hadir Mahasiswa ' . auth()->user()->nama_prodi,
            'data' => $daftar_hadir
        ], 200);
    }

    public function showByPembimbing($id)
    {
        $daftar_hadir = DaftarHadir::where('id_mahasiswa', $id)
            ->orderBy('minggu')
            ->get();

        $grouped = $daftar_hadir->groupBy('minggu')->map(function ($item) {
            return [
                'minggu' => $item[0]->minggu,
                'data_kehadiran' => $item->map(function ($subitem) {
                    return [
                        'id_daftar_hadir' => $subitem->id_daftar_hadir,
                        'id_mahasiswa' => $subitem->id_mahasiswa,
                        'hari_tanggal' => $subitem->hari_tanggal,
                        'minggu' => $subitem->minggu,
                        'tanda-tangan' => asset('/storage/tanda-tangan/' . $subitem->tanda_tangan),
                    ];
                }),
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar Hadir',
            'data' => $grouped->values(),
        ], 200);
    }

    public function showByProdi($id)
    {
        $daftar_hadir = DaftarHadir::where('id_mahasiswa', $id)
            ->orderBy('minggu')
            ->get();

        $grouped = $daftar_hadir->groupBy('minggu')->map(function ($item) {
            return [
                'minggu' => $item[0]->minggu,
                'data_kehadiran' => $item->map(function ($subitem) {
                    return [
                        'id_daftar_hadir' => $subitem->id_daftar_hadir,
                        'id_mahasiswa' => $subitem->id_mahasiswa,
                        'hari_tanggal' => $subitem->hari_tanggal,
                        'minggu' => $subitem->minggu,
                        'tanda_tangan' => asset('/storage/tanda-tangan/' . $subitem->tanda_tangan),
                    ];
                }),
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar Hadir',
            'data' => $grouped->values(),
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hari_tanggal' => 'required',
            'minggu' => 'required',
            'tanda_tangan' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        // Ambil id tempat pkl
        $id_tempat_pkl = DB::table('mahasiswa')
            ->join('pengajuan_pkl', 'mahasiswa.id_mahasiswa', '=', 'pengajuan_pkl.id_mahasiswa')
            ->join('tempat_pkl', 'pengajuan_pkl.id_pengajuan', '=', 'tempat_pkl.id_pengajuan')
            ->select('tempat_pkl.id_tempat_pkl',)
            ->where('mahasiswa.id_mahasiswa', Auth::user()->id_mahasiswa)
            ->first();

        $file = $request->file('tanda_tangan');
        $destinationPath = "public/tanda-tangan";
        $filename = 'tanda-tangan_' . date("Ymd_his") . '_' . $request->user()->nim . '.' . $file->extension();
        $daftar_hadir = DaftarHadir::create([
            'id_mahasiswa' => Auth::id(),
            'id_tempat_pkl' => $id_tempat_pkl->id_tempat_pkl,
            'hari_tanggal' => $request->hari_tanggal,
            'minggu' => $request->minggu,
            'tanda_tangan' => $filename
        ]);
        Storage::putFileAs($destinationPath, $file, $filename);

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar Hadir Berhasil Ditambahkan',
            'data' => new DaftarHadirResource($daftar_hadir)
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $daftar_hadir = DaftarHadir::findOrFail($id);

        if ($request->hasFile('tanda_tangan')) {
            $file = $request->file('tanda_tangan');
            $destinationPath = "public/tanda-tangan";
            $filename = 'tanda-tangan_' . date("Ymd_his") . '_' . $request->user()->nim . '.' . $file->extension();
            Storage::putFileAs($destinationPath, $file, $filename);
            Storage::delete('publc/tanda-tangan/' . $daftar_hadir->tanda_tangan);
            $daftar_hadir->tanda_tangan = $filename;
            $daftar_hadir->update($request->except('tanda_tangan'));
        } else {
            $daftar_hadir->update($request->all());
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar Hadir Berhasil Diperbarui',
            'data' => new DaftarHadirResource($daftar_hadir)
        ], 200);
    }

    public function destroy($id)
    {
        $daftar_hadir = DaftarHadir::findOrFail($id);
        Storage::delete('public/tanda-tangan/' . $daftar_hadir->tanda_tangan);
        $daftar_hadir->delete();

        if ($daftar_hadir != null) {
            return response()->json([
                'message' => 'Daftar Hadir Terhapus',
                'data' => $daftar_hadir
            ]);
        } else {
            return response()->json([
                'message' => 'Daftar Hadir Tidak Ditemukan',
            ], 404);
        }
    }
}
