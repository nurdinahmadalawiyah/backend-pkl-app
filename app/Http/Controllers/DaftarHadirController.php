<?php

namespace App\Http\Controllers;

use App\Http\Resources\DaftarHadirResource;
use App\Models\DaftarHadir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DaftarHadirController extends Controller
{
    public function index(Request $request)
    {
        $daftar_hadir = DaftarHadir::where('id_mahasiswa', $request->user()->id_mahasiswa)
            ->orderBy('minggu')
            ->get()
            ->groupBy('minggu');

        $transformed_daftar_hadir = $daftar_hadir->map(function ($items) {
            return DaftarHadirResource::collection($items);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar Hadir '  . $request->user()->nama,
            'data' => $transformed_daftar_hadir
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

        $file = $request->file('tanda_tangan');
        $destinationPath = "public/tanda-tangan";
        $filename = 'tanda-tangan_' . date("Ymd_his") . '_' . $request->user()->nim . '.' . $file->extension();
        $daftar_hadir = DaftarHadir::create([
            'id_mahasiswa' => Auth::id(),
            'hari_tanggal' => $request->hari_tanggal,
            'minggu' => $request->minggu,
            'tanda_tangan' => $filename
        ]);
        Storage::putFileAs($destinationPath, $file, $filename);

        return response()->json([
            'status' => 'success',
            'message' => 'Jurnal Kegiatan Berhasil Ditambahkan',
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
            'message' => 'Jurnal Kegiatan Berhasil Diperbarui',
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
