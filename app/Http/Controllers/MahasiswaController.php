<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class MahasiswaController extends Controller
{
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

        if (! $token = auth()->guard('mahasiswa_api')->attempt($validator->validated())) {
            return response()->json([
                'status' => false,
                'message' => 'Username atau Password Salah',
            ], 400);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(auth('mahasiswa_api')->user());
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
        ]);
    }

    public function updateProfile(Request $request)
    {
        $mahasiswa = auth('mahasiswa_api')->user();

        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|unique:mahasiswa,email,'.$mahasiswa->id_mahasiswa.',id_mahasiswa',
            'username' => 'nullable|unique:mahasiswa,username,'.$mahasiswa->id_mahasiswa.',id_mahasiswa',
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
