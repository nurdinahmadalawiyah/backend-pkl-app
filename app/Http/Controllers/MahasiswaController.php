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
    public function index()
    {
        $mahasiswa = DB::table('mahasiswa')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select('id_mahasiswa', 'mahasiswa.nama', 'mahasiswa.nim', 'prodi.nama_prodi', 'mahasiswa.semester', 'mahasiswa.email', 'mahasiswa.username', 'mahasiswa.nomor_hp')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Semua Data Mahasiswa Politeknik TEDC Bandung',
            'data' => $mahasiswa
        ], 200);
    }

    public function listByProdi()
    {
        $mahasiswa = DB::table('mahasiswa')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select('id_mahasiswa', 'mahasiswa.nama', 'mahasiswa.nim', 'prodi.nama_prodi', 'mahasiswa.semester', 'mahasiswa.email', 'mahasiswa.username', 'mahasiswa.nomor_hp')
            ->where('mahasiswa.prodi', Auth::user()->id_prodi)
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Semua Data Mahasiswa Politeknik TEDC Bandung',
            'data' => $mahasiswa
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
            'password' => 'required|string|min:8'
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


        return response()->json([
            'status' => 'success',
            'message' => 'Profile',
            'data' => $mahasiswa
        ], 200);
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
            'semester' => 'nullable',
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

        if ($request->has('semester')) {
            $mahasiswa->semester = $request->semester;
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
            'semester' => 'required|string',
            'email' => 'nullable|string',
            'nomor_hp' => 'nullable',
            'password' => 'required|string|min:8',
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

        $mahasiswa->update($request->all());

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
}
