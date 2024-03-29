<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class ProdiController extends Controller
{
    public function index()
    {
        $prodi = Prodi::all();

        return response()->json([
            'status' => 'success',
            'message' => 'Semua Data Prodi',
            'data' => $prodi
        ], 200);
    }

    public function indexByProdi()
    {
        $prodi = Prodi::where('id_prodi', Auth::id())->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data Prodi',
            'data' => $prodi
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

        if (!$token = auth()->guard('prodi_api')->attempt($validator->validated())) {
            return response()->json([
                'status' => false,
                'message' => 'Username atau Password Salah',
            ], 400);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(auth('prodi_api')->user());
    }

    public function logout()
    {
        auth('prodi_api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('prodi_api')->refresh());
    }

    protected function respondWithToken($token)
    {
        $minutes = auth()->guard('prodi_api')->factory()->getTTL() * 60;
        $timestamp = now()->addMinute($minutes);
        $expires_at = date('M d, Y H:i A', strtotime($timestamp));
        return response()->json([
            'status' => true,
            'message' => 'Login berhasil',
            'role' => 'Prodi',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_at' => $expires_at
        ], 200);
    }

    // public function register(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'username' => 'required|string',
    //         'password' => 'required|string|min:8',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 'false',
    //             'message' => 'Invalid Inputs',
    //             'errors' => $validator->errors()
    //         ], 401);
    //     }

    //     $prodi = Prodi::create(array_merge(
    //         $validator->validated(),
    //         ['password' => bcrypt($request->password)]
    //     ));

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Prodi successfully registered',
    //         'user' => $prodi
    //     ], 201);
    // }
}
