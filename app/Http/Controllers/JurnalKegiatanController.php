<?php

namespace App\Http\Controllers;

use App\Models\JurnalKegiatan;
use App\Models\Mahasiswa;
use App\Models\PengajuanPKL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PDF;

class JurnalKegiatanController extends Controller
{
    public function indexByUser(Request $request)
    {
        $jurnal_kegiatan = JurnalKegiatan::where('id_mahasiswa', $request->user()->id_mahasiswa)
            ->orderBy('minggu')
            ->get();

        Storage::deleteDirectory('public/jurnal-kegiatan/');

        $grouped = $jurnal_kegiatan->groupBy('minggu')->map(function ($item) {

            $data_jurnal = DB::table('jurnal_kegiatan')
                ->join('mahasiswa', 'jurnal_kegiatan.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
                ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
                ->join('tempat_pkl', 'jurnal_kegiatan.id_tempat_pkl', '=', 'tempat_pkl.id_tempat_pkl')
                ->join('pembimbing', 'tempat_pkl.id_pembimbing', '=', 'pembimbing.id_pembimbing')
                ->leftJoin('biodata_industri', 'tempat_pkl.id_tempat_pkl', '=', 'biodata_industri.id_tempat_pkl')
                ->select('mahasiswa.nama', 'mahasiswa.nim', 'jurnal_kegiatan.minggu', 'prodi.nama_prodi', 'pembimbing.nama as nama_pembimbing', 'pembimbing.nik', 'biodata_industri.nama_industri', 'biodata_industri.alamat_kantor')
                ->where('jurnal_kegiatan.id_mahasiswa', Auth::user()->id_mahasiswa)
                ->first();

            $pdf = PDF::loadView('pdf.jurnal_kegiatan', ['grouped' => $item, 'data_jurnal' => $data_jurnal])->setPaper('a4');
            $filename = 'jurnal_kegiatan_minggu_' . $item[0]->minggu . '_' . Auth::user()->nim . '.pdf';
            Storage::put('public/jurnal-kegiatan/' . $filename, $pdf->output());
            $pdf_url = asset('/storage/jurnal-kegiatan/' . $filename);

            return [
                'minggu' => $item[0]->minggu,
                'pdf_url' => $pdf_url,
                'data_kegiatan' => $item->map(function ($subitem) {
                    return [
                        'id_jurnal_kegiatan' => $subitem->id_jurnal_kegiatan,
                        'id_mahasiswa' => $subitem->id_mahasiswa,
                        'tanggal' => $subitem->tanggal,
                        'minggu' => $subitem->minggu,
                        'bidang_pekerjaan' => $subitem->bidang_pekerjaan,
                        'keterangan' => $subitem->keterangan,
                    ];
                }),
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Jurnal Kegiatan ' . $request->user()->nama,
            'data' => $grouped->values(),
        ], 200);
    }

    public function indexByProdi($id)
    {
        $jurnal_kegiatan = JurnalKegiatan::where('id_mahasiswa', $id)
            ->orderBy('minggu')
            ->get();

        $grouped = $jurnal_kegiatan->groupBy('minggu')->map(function ($item) {
            return [
                'minggu' => $item[0]->minggu,
                'data_kegiatan' => $item->map(function ($subitem) {
                    return [
                        'id_jurnal_kegiatan' => $subitem->id_jurnal_kegiatan,
                        'id_mahasiswa' => $subitem->id_mahasiswa,
                        'tanggal' => $subitem->tanggal,
                        'minggu' => $subitem->minggu,
                        'bidang_pekerjaan' => $subitem->bidang_pekerjaan,
                        'keterangan' => $subitem->keterangan,
                    ];
                }),
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Jurnal Kegiatan',
            'data' => $grouped->values(),
        ], 200);
    }

    public function showByPembimbing($id)
    {
        $jurnal_kegiatan = JurnalKegiatan::where('id_mahasiswa', $id)
            ->orderBy('minggu')
            ->get();

        $grouped = $jurnal_kegiatan->groupBy('minggu')->map(function ($item) {
            return [
                'minggu' => $item[0]->minggu,
                'data_kegiatan' => $item->map(function ($subitem) {
                    return [
                        'id_jurnal_kegiatan' => $subitem->id_jurnal_kegiatan,
                        'id_mahasiswa' => $subitem->id_mahasiswa,
                        'tanggal' => $subitem->tanggal,
                        'minggu' => $subitem->minggu,
                        'bidang_pekerjaan' => $subitem->bidang_pekerjaan,
                        'keterangan' => $subitem->keterangan,
                    ];
                }),
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Jurnal Kegiatan',
            'data' => $grouped->values(),
        ], 200);
    }

    public function showByProdi()
    {
        $id_prodi = auth()->user()->id_prodi;

        $jurnal_kegiatan = DB::table('mahasiswa')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select('mahasiswa.id_mahasiswa', 'mahasiswa.nama', 'mahasiswa.nim', 'prodi.nama_prodi', 'mahasiswa.prodi')
            ->where('mahasiswa.prodi', $id_prodi)
            ->get();

        if (is_null($jurnal_kegiatan)) {
            return response()->json(['error' => 'Data Tidak Ditemukan.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Jurnal Kegiatan Mahasiswa ' . auth()->user()->nama_prodi,
            'data' => $jurnal_kegiatan
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required',
            'minggu' => 'required',
            'bidang_pekerjaan' => 'required',
            'keterangan' => 'required',
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

        // Ambil id tempat pkl
        $id_tempat_pkl = DB::table('mahasiswa')
            ->join('pengajuan_pkl', 'mahasiswa.id_mahasiswa', '=', 'pengajuan_pkl.id_mahasiswa')
            ->join('tempat_pkl', 'pengajuan_pkl.id_pengajuan', '=', 'tempat_pkl.id_pengajuan')
            ->select('tempat_pkl.id_tempat_pkl',)
            ->where('mahasiswa.id_mahasiswa', Auth::user()->id_mahasiswa)
            ->first();

        $jurnal_kegiatan = JurnalKegiatan::create([
            'id_mahasiswa' => Auth::id(),
            'id_tempat_pkl' => $id_tempat_pkl->id_tempat_pkl,
            'tanggal' => $request->tanggal,
            'minggu' => $request->minggu,
            'bidang_pekerjaan' => $request->bidang_pekerjaan,
            'keterangan' => $request->keterangan
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Jurnal Kegiatan Berhasil Ditambahkan',
            'data' => $jurnal_kegiatan
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

        $jurnal_kegiatan = JurnalKegiatan::findOrFail($id);

        $jurnal_kegiatan->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Jurnal Kegiatan Berhasil Diperbarui',
            'data' => $jurnal_kegiatan
        ], 200);
    }

    public function destroy($id)
    {
        $jurnal_kegiatan = JurnalKegiatan::findOrFail($id);
        $jurnal_kegiatan->delete();

        if ($jurnal_kegiatan != null) {
            return response()->json([
                'message' => 'Jurnal Kegiatan Dihapus',
                'data' => $jurnal_kegiatan
            ], 200);
        } else {
            return response()->json([
                'message' => 'Jurnal Gagal Kegiatan',
            ], 404);
        }
    }
}
