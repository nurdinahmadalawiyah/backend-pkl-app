<?php

namespace App\Http\Controllers;

use App\Models\CatatanKhusus;
use App\Models\PengajuanPKL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PDF;

class CatatanKhususController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'catatan' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $pengajuan_pkl = PengajuanPKL::where('id_mahasiswa', $request->user()->id_mahasiswa)
            ->where('status', 'disetujui')
            ->first();

        if (!$pengajuan_pkl) {
            return response()->json(['message' => 'Pengajuan pkl belum disetujui'], 401);
        }

        $catatan_khusus = CatatanKhusus::where('id_mahasiswa', Auth::user()->id_mahasiswa)->first();
    
        $id_tempat_pkl = DB::table('mahasiswa')
            ->join('pengajuan_pkl', 'mahasiswa.id_mahasiswa', '=', 'pengajuan_pkl.id_mahasiswa')
            ->join('tempat_pkl', 'pengajuan_pkl.id_pengajuan', '=', 'tempat_pkl.id_pengajuan')
            ->select('tempat_pkl.id_tempat_pkl',)
            ->where('mahasiswa.id_mahasiswa', Auth::user()->id_mahasiswa)
            ->first();

        if ($catatan_khusus) {
            $catatan_khusus->update([
                'id_tempat_pkl' => $id_tempat_pkl->id_tempat_pkl,
                'catatan' => $request->catatan,
            ]);
        } else {
            $catatan_khusus = CatatanKhusus::create([
                'id_mahasiswa' => Auth::user()->id_mahasiswa,
                'id_tempat_pkl' => $id_tempat_pkl->id_tempat_pkl,
                'catatan' => $request->catatan,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Catatan Khusus Berhasil Disimpan',
            'data' => $catatan_khusus
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CatatanKhusus  $catatanKhusus
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $id_mahasiswa = Auth::user()->id_mahasiswa;

        $catatan_khusus = DB::table('catatan_khusus')
            ->join('mahasiswa', 'catatan_khusus.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
            ->select('catatan_khusus.*', 'mahasiswa.nama', 'mahasiswa.nim')
            ->where('catatan_khusus.id_mahasiswa', $id_mahasiswa)
            ->first();

        if (is_null($catatan_khusus)) {
            return response()->json(['error' => 'Data Tidak Ditemukan.'], 404);
        }
    
        $pdf = PDF::loadView('pdf.catatan_khusus', compact('catatan_khusus'))
        ->setPaper('a4');

        $filename = 'catatan_khusus_' . $catatan_khusus->nim . '.pdf';

        Storage::put('public/catatan-khusus/' . $filename, $pdf->output());

        $pdf_url = asset('/storage/catatan-khusus/' . $filename);

        return response()->json([
            'status' => 'success',
            'message' => 'Detail catatan-khusus',
            'pdf_url' => $pdf_url,
            'data' => $catatan_khusus
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CatatanKhusus  $catatanKhusus
     * @return \Illuminate\Http\Response
     */
    public function edit(CatatanKhusus $catatanKhusus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CatatanKhusus  $catatanKhusus
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CatatanKhusus $catatanKhusus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CatatanKhusus  $catatanKhusus
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $catatan_khusus = CatatanKhusus::where('id_mahasiswa', Auth::user()->id_mahasiswa)->first();

        $biodata = DB::table('mahasiswa')
            ->join('catatan_khusus', 'mahasiswa.id_mahasiswa', '=', 'catatan_khusus.id_mahasiswa')
            ->select('catatan_khusus.id_catatan_khusus', 'mahasiswa.nim')
            ->where('catatan_khusus.id_mahasiswa', Auth::user()->id_mahasiswa)
            ->first();

        if ($catatan_khusus != null) {
            $filename = 'catatan_khusus_' . Auth::user()->nim . '.pdf';
            Storage::delete('public/catatan-khusus/' . $filename);
            $catatan_khusus->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Catatan Khusus Dihapus',
                'data' => $catatan_khusus
            ], 200);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Catatan Khusus Tidak Ditemukan',
            ], 404);
        }
    }
}
