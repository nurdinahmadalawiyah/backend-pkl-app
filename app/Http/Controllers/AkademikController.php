<?php

namespace App\Http\Controllers;

use App\Models\Akademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AkademikController extends Controller
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

        if (! $token = auth()->guard('akademik_api')->attempt($validator->validated())) {
            return response()->json([
                'status' => false,
                'message' => 'Username atau Password Salah',
            ], 400);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(auth('akademik_api')->user());
    }

    public function logout()
    {
        auth('akademik_api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('akademik_api')->refresh());
    }

    protected function respondWithToken($token)
    {
        $minutes = auth()->guard('akademik_api')->factory()->getTTL() * 60;
        $timestamp = now()->addMinute($minutes);
        $expires_at = date('M d, Y H:i A', strtotime($timestamp));
        return response()->json([
            'status' => true,
            'message' => 'Login berhasil',
            'role' => 'Akademik',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_at' => $expires_at
        ], 200);
    }

    public function savePlayerId(Request $request)
    {    
        $akademik = Akademik::first();
        if (!$akademik) {
            return response()->json(['message' => 'Data Akademik tidak ditemukan'], 404);
        }
        
        $notification_id = $request->input('notification_id');
        DB::table('akademik')->where('id_akademik', $akademik->id_akademik)->update(['notification_id' => $notification_id]);
    
        return response()->json(['message' => 'Player ID berhasil disimpan'], 200);
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

    //     $akademik = Akademik::create(array_merge(
    //         $validator->validated(),
    //         ['password' => bcrypt($request->password)]
    //     ));

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Akademik successfully registered',
    //         'user' => $akademik
    //     ], 201);
    // }
}