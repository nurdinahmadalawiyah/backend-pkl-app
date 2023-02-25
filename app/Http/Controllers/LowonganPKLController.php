<?php

namespace App\Http\Controllers;

use App\Http\Resources\LowonganPKLResource;
use App\Models\LowonganPKL;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LowonganPKLController extends Controller
{
    public function index()
    {
        $lowongan_pkl = LowonganPKL::all();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Semua Data Lowongan PKL',
            'data' => LowonganPKLResource::collection($lowongan_pkl)
        ], 200);
    }

    public function showByProdi()
    {
        $prodi = Auth::user();
    
        if (!$prodi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data prodi tidak ditemukan'
            ], 404);
        }
    
        $lowongan_pkl = LowonganPKL::where('id_prodi', $prodi->id_prodi)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data Lowongan PKL',
            'data' => LowonganPKLResource::Collection($lowongan_pkl)
        ], 200);
    }

    public function searchByKeyword(Request $request)
    {
        $keyword = $request->query('q');

        $lowongan_pkl = LowonganPKL::where('posisi', 'like', '%' . $keyword . '%')
            ->orWhere('nama_perusahaan', 'like', '%' . $keyword . '%')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Hasil Pencarian',
            'data' => LowonganPKLResource::collection($lowongan_pkl)
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'posisi' => 'required',
            'nama_perusahaan' => 'required',
            'alamat_perusahaan' => 'required',
            'gambar' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $file = $request->file('gambar');
        $destinationPath = "public\images";
        $filename = 'lowongan_pkl_' . date("Ymd_his") . '.' . $file->extension();
        $lowongan_pkl = LowonganPKL::create([
            'id_prodi' => Auth::id(),
            'posisi' => $request->posisi,
            'nama_perusahaan' => $request->nama_perusahaan,
            'alamat_perusahaan' => $request->alamat_perusahaan,
            'gambar' => $filename,
            'url' => $request->url,
        ]);
        Storage::putFileAs($destinationPath, $file, $filename);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Lowongan PKL',
            'data' => new LowonganPKLResource($lowongan_pkl)
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $lowongan_pkl = LowonganPKL::findOrFail($id);

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $destinationPath = "public\images";
            $filename = 'lowongan_pkl_' . date("Ymd_his") . '.' . $file->extension();
            Storage::putFileAs($destinationPath, $file, $filename);
            Storage::delete('public/images/' . $lowongan_pkl->gambar);
            $lowongan_pkl->gambar = $filename;
            $lowongan_pkl->update($request->except('gambar'));
        } else {
            $lowongan_pkl->update($request->all());
        }
        

        return response()->json([
            'status' => 'success',
            'message' => 'Data Lowongan PKL',
            'data' => new LowonganPKLResource($lowongan_pkl)
        ], 200);
    }

    public function destroy($id)
    {
        $lowongan_pkl = LowonganPKL::findOrFail($id);
        Storage::delete('public/images/' . $lowongan_pkl->gambar);
        $lowongan_pkl->delete();

        if ($lowongan_pkl != null) {
            return response()->json([
                'message' => 'Lowongan PKL Terhapus',
                'data' => $lowongan_pkl
            ]);
        } else {
            return response()->json([
                'message' => 'Lowongan PKL Tidak Ditemukan',
            ], 404);
        }
    }
}
