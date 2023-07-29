<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Tymon\JWTAuth\Facades\JWTAuth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function checkToken()
    {
        try {
            JWTAuth::parseToken()->authenticate();
            return response()->json(['message' => 'Token valid']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Token tidak valid'], 401);
        }
    }

}
