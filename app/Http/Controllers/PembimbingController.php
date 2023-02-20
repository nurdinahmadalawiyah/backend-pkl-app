<?php

namespace App\Http\Controllers;

use App\Models\Pembimbing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PembimbingController extends Controller
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

        if (! $token = auth()->guard('pembimbing_api')->attempt($validator->validated())) {
            return response()->json([
                'status' => false,
                'message' => 'Username atau Password Salah',
            ], 400);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(auth('pembimbing_api')->user());
    }

    public function logout()
    {
        auth('pembimbing_api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('pembimbing_api')->refresh());
    }

    protected function respondWithToken($token)
    {
        $minutes = auth()->guard('pembimbing_api')->factory()->getTTL() * 60;
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

    //     $pembimbing = Pembimbing::create(array_merge(
    //         $validator->validated(),
    //         ['password' => bcrypt($request->password)]
    //     ));

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Pembimbing successfully registered',
    //         'user' => $pembimbing
    //     ], 201);
    // }
}
