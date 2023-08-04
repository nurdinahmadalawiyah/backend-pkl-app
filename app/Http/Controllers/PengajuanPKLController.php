<?php

namespace App\Http\Controllers;

use App\Models\Akademik;
use App\Models\PengajuanPKL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PDF;
use Illuminate\Support\Facades\Http;

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

        $data_pengajuan = collect();
        foreach ($pengajuan as $item) {
            $data_pengajuan->push([
                'id_pengajuan' => $item->id_pengajuan,
                'id_mahasiswa' => $item->id_mahasiswa,
                'nama_perusahaan' => $item->nama_perusahaan,
                'alamat_perusahaan' => $item->alamat_perusahaan,
                'tanggal_mulai' => $item->tanggal_mulai,
                'tanggal_selesai' => $item->tanggal_selesai,
                'status' => $item->status,
                'surat' => $item->surat ? asset('/storage/surat-pengantar-pkl/' . $item->surat) : null,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Status Pengajuan PKL',
            'user' => $prodi,
            'data' => $data_pengajuan
        ], 200);
    }

    public function index()
    {
        $pengajuan_pkl = DB::table('pengajuan_pkl')
            ->join('mahasiswa', 'pengajuan_pkl.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select('pengajuan_pkl.*', 'mahasiswa.nama', 'mahasiswa.nim', 'prodi.nama_prodi')
            ->get();

        $data = $pengajuan_pkl->map(function ($item) {
            return [
                'id_pengajuan' => $item->id_pengajuan,
                'id_mahasiswa' => $item->id_mahasiswa,
                'nama' => $item->nama,
                'nim' => $item->nim,
                'nama_prodi' => $item->nama_prodi,
                'nama_perusahaan' => $item->nama_perusahaan,
                'alamat_perusahaan' => $item->alamat_perusahaan,
                'tanggal_mulai' => $item->tanggal_mulai,
                'tanggal_selesai' => $item->tanggal_selesai,
                'status' => $item->status,
                'surat' => asset('/storage/surat-pengantar-pkl/' . $item->surat),
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Semua Data Pengajuan PKL',
            'data' => $data
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
                'updated_at' => $pengajuan_pkl->updated_at,
                'nama' => $pengajuan_pkl->nama,
                'nim' => $pengajuan_pkl->nim,
                'nama_prodi' => $pengajuan_pkl->nama_prodi,
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        $akademik = Akademik::first();

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

        $this->sendNotificationToAdmin($akademik->notification_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengajuan Berhasil Terkirim',
            'data' => $pengajuan
        ], 200);
    }

    public function sendNotificationToAdmin($notificationId)
    {
        $app_id = '87cf8313-c7f7-420a-b2dd-fbf5a3e29513';
        $api_key = 'NjUzNjgyYWItOTE0Zi00NGQ4LTg1NWUtMzdmNjIwZjFmZDYw';

        Http::withHeaders([
            'Authorization' => 'Basic ' . $api_key,
            'Content-Type' => 'application/json',
        ])->post('https://onesignal.com/api/v1/notifications', [
            'app_id' => $app_id,
            'include_player_ids' => [$notificationId],
            'contents' => ['en' => "Ada Pengajuan PKL Baru dari Mahasiswa"],
            'headings' => ['en' => "Pengajuan Masuk"],
        ]);
    }

    public function setujuiPengajuan($id)
    {
        $pengajuan_pkl = PengajuanPKL::findOrFail($id);

        $data_surat = DB::table('mahasiswa')
            ->join('pengajuan_pkl', 'mahasiswa.id_mahasiswa', '=', 'pengajuan_pkl.id_mahasiswa')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select('mahasiswa.nama', 'mahasiswa.nim', 'prodi.nama_prodi', 'mahasiswa.semester', 'prodi.nama_ketua_prodi', 'prodi.nidn_ketua_prodi', 'mahasiswa.notification_id')
            ->where('pengajuan_pkl.id_pengajuan', '=', $id)
            ->first();

        $pdf = PDF::loadView('pdf.surat_pengantar_pkl', compact(['pengajuan_pkl', 'data_surat']))
            ->setPaper('a4');

        $filename = 'surat_pengantar_pkl_' . $data_surat->nim . '.pdf';
        Storage::put('public/surat-pengantar-pkl/' . $filename, $pdf->output());

        $pengajuan_pkl->surat = $filename;
        $pengajuan_pkl->status = 'disetujui';
        $pengajuan_pkl->save();

        $this->sendNotification($data_surat->notification_id);

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

    public function sendNotification($notificationId)
    {
        $app_id = 'f8bc8286-9b49-4347-995c-1885262c4dc3';
        $api_key = 'ZjlkZGM5ZDktOTNkOS00ZGVlLTgwY2YtNDJjMWZlMjQwOTBj';

        Http::withHeaders([
            'Authorization' => 'Basic ' . $api_key,
            'Content-Type' => 'application/json',
        ])->post('https://onesignal.com/api/v1/notifications', [
            'app_id' => $app_id,
            'include_player_ids' => [$notificationId],
            'contents' => ['en' => "Selamat pengajuan PKL anda telah disetujui"],
            'headings' => ['en' => "Selamat"],
        ]);
    }

    public function tolakPengajuan($id)
    {
        $pengajuan_pkl = PengajuanPKL::findOrFail($id);

        $data_surat = DB::table('mahasiswa')
            ->join('pengajuan_pkl', 'mahasiswa.id_mahasiswa', '=', 'pengajuan_pkl.id_mahasiswa')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select('mahasiswa.nama', 'mahasiswa.nim', 'prodi.nama_prodi', 'mahasiswa.semester', 'prodi.nama_ketua_prodi', 'prodi.nidn_ketua_prodi', 'mahasiswa.notification_id')
            ->where('pengajuan_pkl.id_pengajuan', '=', $id)
            ->first();

        $filename = 'surat_pengantar_pkl_' . $data_surat->nim . '.pdf';
        Storage::delete('public/surat-pengantar-pkl/' . $filename);

        $pengajuan_pkl->surat = null;
        $pengajuan_pkl->status = 'ditolak';
        $pengajuan_pkl->save();

        $this->sendRejectNotification($data_surat->notification_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengajuan pkl berhasil ditolak',
            'data' => $pengajuan_pkl,
        ]);
    }

    public function sendRejectNotification($notificationId)
    {
        $app_id = 'f8bc8286-9b49-4347-995c-1885262c4dc3';
        $api_key = 'ZjlkZGM5ZDktOTNkOS00ZGVlLTgwY2YtNDJjMWZlMjQwOTBj';

        Http::withHeaders([
            'Authorization' => 'Basic ' . $api_key,
            'Content-Type' => 'application/json',
        ])->post('https://onesignal.com/api/v1/notifications', [
            'app_id' => $app_id,
            'include_player_ids' => [$notificationId],
            'contents' => ['en' => "Mohon maaf pengajuan PKL anda ditolak, periksa kembali data pengajuan PKL anda"],
            'headings' => ['en' => "Maaf"],
        ]);
    }
}
