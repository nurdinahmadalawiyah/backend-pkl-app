<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $tahunMasuk = $request->input('tahun_masuk');

        $query = DB::table('mahasiswa')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select('id_mahasiswa', 'mahasiswa.nama', 'mahasiswa.nim', 'prodi.nama_prodi', 'prodi.id_prodi', 'mahasiswa.tahun_masuk', 'mahasiswa.email', 'mahasiswa.username', 'mahasiswa.nomor_hp')
            ->orderByDesc('mahasiswa.updated_at');

        if (!empty($tahunMasuk)) {
            $query->where('mahasiswa.tahun_masuk', '=', $tahunMasuk);
        }

        $mahasiswa = $query->get();

        $mahasiswaData = [];
        foreach ($mahasiswa as $data) {
            $semester = $this->hitungSemester($data->tahun_masuk);
            $mahasiswaData[] = [
                "id_mahasiswa" => $data->id_mahasiswa,
                "nama" => $data->nama,
                "nim" => $data->nim,
                "nama_prodi" => $data->nama_prodi,
                "id_prodi" => $data->id_prodi,
                "tahun_masuk" => $data->tahun_masuk,
                "semester" => $semester,
                "email" => $data->email,
                "username" => $data->username,
                "nomor_hp" => $data->nomor_hp
            ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Semua Data Mahasiswa Politeknik TEDC Bandung',
            'data' => $mahasiswaData
        ], 200);
    }

    public function listByProdi(Request $request)
    {
        $tahunMasuk = $request->input('tahun_masuk');

        $query = DB::table('mahasiswa')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select('id_mahasiswa', 'mahasiswa.nama', 'mahasiswa.nim', 'prodi.nama_prodi', 'prodi.id_prodi', 'mahasiswa.tahun_masuk', 'mahasiswa.email', 'mahasiswa.username', 'mahasiswa.nomor_hp')
            ->where('mahasiswa.prodi', Auth::user()->id_prodi);

        if (!empty($tahunMasuk)) {
            $query->where('mahasiswa.tahun_masuk', '=', $tahunMasuk);
        }

        $mahasiswa = $query->get();

        $mahasiswaData = [];
        foreach ($mahasiswa as $data) {
            $semester = $this->hitungSemester($data->tahun_masuk);
            $mahasiswaData[] = [
                "id_mahasiswa" => $data->id_mahasiswa,
                "nama" => $data->nama,
                "nim" => $data->nim,
                "nama_prodi" => $data->nama_prodi,
                "id_prodi" => $data->id_prodi,
                "semester" => $semester,
                "tahun_masuk" => $data->tahun_masuk,
                "email" => $data->email,
                "username" => $data->username,
                "nomor_hp" => $data->nomor_hp
            ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Semua Data Mahasiswa Politeknik TEDC Bandung',
            'data' => $mahasiswaData
        ], 200);
    }

    public function lovTahunMasuk()
    {
        $tahunMasukValues = DB::table('mahasiswa')
            ->select('tahun_masuk')
            ->distinct()
            ->pluck('tahun_masuk');
    
        $tahunMasukArray = $tahunMasukValues->toArray();
    
        return response()->json([
            'status' => 'success',
            'message' => 'List of Tahun Masuk',
            'data' => array_map(function ($tahun) {
                return ['tahun_masuk' => $tahun];
            }, $tahunMasukArray),
        ], 200);
    }
    
    public function listByPembimbing()
    {
        $id_pembimbing = auth()->user()->id_pembimbing;

        $list_mahasiswa = DB::table('tempat_pkl')
            ->join('pengajuan_pkl', 'tempat_pkl.id_pengajuan', '=', 'pengajuan_pkl.id_pengajuan')
            ->join('mahasiswa', 'pengajuan_pkl.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
            ->leftJoin('pembimbing', 'tempat_pkl.id_pembimbing', '=', 'pembimbing.id_pembimbing')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select('tempat_pkl.id_tempat_pkl', 'mahasiswa.id_mahasiswa', 'mahasiswa.nama as nama_mahasiswa', 'prodi.nama_prodi', 'mahasiswa.nim', 'pembimbing.nama as nama_pembimbing', 'pembimbing.nik')
            ->where('tempat_pkl.id_pembimbing', $id_pembimbing)
            ->get();

        if (is_null($list_mahasiswa)) {
            return response()->json(['error' => 'Data Tidak Ditemukan.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar Mahasiswa',
            'data' => $list_mahasiswa,
        ], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Inputs',
                'error' => $validator->errors()
            ], 422);
        }

        if (!$token = auth()->guard('mahasiswa_api')->attempt($validator->validated())) {
            return response()->json([
                'status' => false,
                'message' => 'Username atau Password Salah',
            ], 400);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        $mahasiswa = Mahasiswa::join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select('mahasiswa.*', 'prodi.nama_prodi')
            ->where('mahasiswa.id_mahasiswa', auth('mahasiswa_api')->user()->id_mahasiswa)
            ->first();

        $semester = $this->hitungSemester($mahasiswa->tahun_masuk);

        return response()->json([
            'status' => 'success',
            'message' => 'Profile',
            'data' => [
                "id_mahasiswa" => $mahasiswa->id_mahasiswa,
                "username" => $mahasiswa->username,
                "nama" => $mahasiswa->nama,
                "nim" => $mahasiswa->nim,
                "prodi" => $mahasiswa->prodi,
                "semester" => $semester,
                "tahun_masuk" => $mahasiswa->tahun_masuk,
                "email" => $mahasiswa->email,
                "nomor_hp" => $mahasiswa->nomor_hp,
                "notification_id" => $mahasiswa->notification_id,
                "created_at" => $mahasiswa->created_at,
                "updated_at" => $mahasiswa->updated_at,
                "nama_prodi" => $mahasiswa->nama_prodi,
            ]
        ], 200);
    }

    public function hitungSemester($tahunMasuk)
    {
        $tahunSekarang = date("Y");

        $selisihTahun = $tahunSekarang - $tahunMasuk;

        if ($selisihTahun < 0) {
            echo "Tahun masuk di masa depan";
        } else {
            $semester = $selisihTahun * 2;

            if (date("n") >= 9) {
                $semester++;
            }

            $angkaSemester = [
                "Satu", "Dua", "Tiga", "Empat", "Lima",
                "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh",
                "Sebelas", "Dua Belas", "Tiga Belas", "Empat Belas",
            ];

            if ($semester >= 1 && $semester <= count($angkaSemester)) {
                return $semester . " (" . $angkaSemester[$semester - 1] . ")";
            } else {
                return "Semester tidak valid";
            }
        }
    }


    public function logout()
    {
        auth('mahasiswa_api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('mahasiswa_api')->refresh());
    }

    protected function respondWithToken($token)
    {
        $minutes = auth()->guard('mahasiswa_api')->factory()->getTTL() * 60;
        $timestamp = now()->addMinute($minutes);
        $expires_at = date('M d, Y H:i A', strtotime($timestamp));
        return response()->json([
            'status' => true,
            'message' => 'Login berhasil',
            'role' => 'Mahasiswa',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_at' => $expires_at
        ], 200);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required',
        ]);

        $mahasiswa = Mahasiswa::find(auth('mahasiswa_api')->id());

        if (!Hash::check($request->input('password_lama'), $mahasiswa->password)) {
            return response()->json([
                'message' => 'Password lama tidak cocok'
            ], 401);
        }

        $mahasiswa->password = Hash::make($request->input('password_baru'));
        $mahasiswa->save();

        return response()->json([
            'message' => 'Password berhasil diubah'
        ], 200);
    }

    public function checkStatus()
    {
        $disetujui = DB::table('pengajuan_pkl')
            ->join('mahasiswa', 'mahasiswa.id_mahasiswa', '=', 'pengajuan_pkl.id_mahasiswa')
            ->leftJoin('tempat_pkl', 'tempat_pkl.id_pengajuan', '=', 'pengajuan_pkl.id_pengajuan')
            ->select('mahasiswa.id_mahasiswa', 'pengajuan_pkl.status', 'tempat_pkl.id_tempat_pkl',)
            ->where('mahasiswa.id_mahasiswa', Auth::user()->id_mahasiswa)
            ->where('pengajuan_pkl.status', 'disetujui')
            ->first();

        $menunggu = DB::table('pengajuan_pkl')
            ->join('mahasiswa', 'mahasiswa.id_mahasiswa', '=', 'pengajuan_pkl.id_mahasiswa')
            ->leftJoin('tempat_pkl', 'tempat_pkl.id_pengajuan', '=', 'pengajuan_pkl.id_pengajuan')
            ->select('mahasiswa.id_mahasiswa', 'pengajuan_pkl.status', 'tempat_pkl.id_tempat_pkl',)
            ->where('mahasiswa.id_mahasiswa', Auth::user()->id_mahasiswa)
            ->where('pengajuan_pkl.status', 'menunggu')
            ->first();

        $ditolak = DB::table('pengajuan_pkl')
            ->join('mahasiswa', 'mahasiswa.id_mahasiswa', '=', 'pengajuan_pkl.id_mahasiswa')
            ->leftJoin('tempat_pkl', 'tempat_pkl.id_pengajuan', '=', 'pengajuan_pkl.id_pengajuan')
            ->select('mahasiswa.id_mahasiswa', 'pengajuan_pkl.status', 'tempat_pkl.id_tempat_pkl',)
            ->where('mahasiswa.id_mahasiswa', Auth::user()->id_mahasiswa)
            ->where('pengajuan_pkl.status', 'ditolak')
            ->first();

        if ($disetujui && $disetujui->id_tempat_pkl != null) {
            return response()->json([
                'message' => 'Status Mahasiswa ' . Auth::user()->nama,
                'data' => [
                    'id_mahasiswa' => $disetujui->id_mahasiswa,
                    'status' => $disetujui->status,
                    'telah_konfirmasi' => "true",
                ]
            ]);
        } elseif ($disetujui) {
            return response()->json([
                'message' => 'Status Mahasiswa ' . Auth::user()->nama,
                'data' => [
                    'id_mahasiswa' => $disetujui->id_mahasiswa,
                    'status' => $disetujui->status,
                    'telah_konfirmasi' => "false",
                ]
            ]);
        } elseif ($menunggu && $menunggu->id_tempat_pkl == null) {
            return response()->json([
                'message' => 'Status Mahasiswa ' . Auth::user()->nama,
                'data' => [
                    'id_mahasiswa' => $menunggu->id_mahasiswa,
                    'status' => $menunggu->status,
                    'telah_konfirmasi' => "false",
                ]
            ]);
        } elseif ($ditolak && $menunggu->id_tempat_pkl == null) {
            return response()->json([
                'message' => 'Status Mahasiswa ' . Auth::user()->nama,
                'data' => [
                    'id_mahasiswa' => $ditolak->id_mahasiswa,
                    'status' => $ditolak->status,
                    'telah_konfirmasi' => "false",
                ]
            ]);
        } else {
            return response()->json([
                'message' => 'Status Mahasiswa ' . Auth::user()->nama,
                'data' => [
                    'id_mahasiswa' => $ditolak->id_mahasiswa,
                    'status' => null,
                    'telah_konfirmasi' => null,
                ]
            ]);
        }
    }

    public function updateProfile(Request $request)
    {
        $mahasiswa = auth('mahasiswa_api')->user();

        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|unique:mahasiswa,email,' . $mahasiswa->id_mahasiswa . ',id_mahasiswa',
            'username' => 'nullable|unique:mahasiswa,username,' . $mahasiswa->id_mahasiswa . ',id_mahasiswa',
            'tahun_masuk' => 'nullable',
            'nomor_hp' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        if ($request->has('email')) {
            $mahasiswa->email = $request->email;
        }

        if ($request->has('username')) {
            $mahasiswa->username = $request->username;
        }

        if ($request->has('tahun_masuk')) {
            $mahasiswa->tahun_masuk = $request->tahun_masuk;
        }

        if ($request->has('nomor_hp')) {
            $mahasiswa->nomor_hp = $request->nomor_hp;
        }

        $mahasiswa->save();

        return response()->json([
            'message' => 'Profile updated successfully.',
            'data' => auth('mahasiswa_api')->user()
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'nama' => 'required',
            'nim' => 'required|string',
            'prodi' => 'required',
            'tahun_masuk' => 'required|string',
            'email' => 'nullable|string',
            'nomor_hp' => 'nullable',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => 'Invalid Inputs',
                'errors' => $validator->errors()
            ], 401);
        }

        $mahasiswa = Mahasiswa::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'status' => true,
            'message' => 'Berhasil Menambah Data Mahasiswa',
            'user' => $mahasiswa
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);

        if ($request->has('password') && !empty($request->input('password'))) {
            $validator = Validator::make($request->all(), [
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 400);
            }

            $password = bcrypt($request->input('password'));
        } else {
            $password = $mahasiswa->password;
        }

        $requestData = $request->except('password');

        $mahasiswa->update(array_merge($requestData, [
            'password' => $password,
        ]));

        return response()->json([
            'status' => true,
            'message' => 'Berhasil Memperbarui Data Mahasiswa',
            'user' => $mahasiswa
        ], 201);
    }

    public function destroy($id)
    {
        $mahasiswa = Mahasiswa::findOrFail($id);
        $mahasiswa->delete();


        if ($mahasiswa != null) {
            return response()->json([
                'message' => 'Data Mahasiswa Dihapus',
                'data' => $mahasiswa
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data Mahasiswa',
            ], 404);
        }
    }

    public function savePlayerId(Request $request)
    {
        $mahasiswa_id = Auth::user()->id_mahasiswa;
        $notification_id = $request->input('notification_id');
        DB::table('mahasiswa')->where('id_mahasiswa', $mahasiswa_id)->update(['notification_id' => $notification_id]);

        return response()->json(['message' => 'Player ID berhasil disimpan'], 200);
    }
}
