<?php

namespace App\Http\Controllers;

use App\Models\JurnalKegiatan;
use App\Models\PengajuanPKL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JurnalKegiatanController extends Controller
{
    public function indexByUser(Request $request)
    {
        $jurnal_kegiatan = JurnalKegiatan::where('id_mahasiswa', $request->user()->id_mahasiswa)
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
            'message' => 'Jurnal Kegiatan ' . $request->user()->nama,
            'data' => $grouped->values(),
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

        $jurnal_kegiatan = JurnalKegiatan::create([
            'id_mahasiswa' => Auth::id(),
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
