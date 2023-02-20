<?php

use App\Http\Controllers\AkademikController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PembimbingController;
use App\Http\Controllers\PengajuanMagangController;
use App\Http\Controllers\TempatMagangController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('pengajuan-magang')->controller(PengajuanMagangController::class)->group(function () {
    Route::middleware('auth:mahasiswa_api')->group(function () {
        Route::post('/', 'store');
        Route::get('status/', 'showAllByUser');
        // Route::post('update/{id}', 'update');
        // Route::delete('/{id}', 'delete');
    });

    Route::middleware('auth:akademik_api')->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
        Route::put('approve-pengajuan/{id}', 'setujuiPengajuan');
    });
});

Route::prefix('tempat-magang')->controller(TempatMagangController::class)->group(function () {
    Route::middleware('auth:mahasiswa_api')->group(function () {
        Route::post('/', 'store');
    });

    Route::middleware('auth:prodi_api')->group(function () {
        Route::get('/', 'index');
    });
});

Route::prefix('akademik')->controller(AkademikController::class)->group(function () {
    Route::post('login', 'login');
    // Route::post('register', 'register');
    Route::middleware('auth:akademik_api')->group(function () {
        Route::post('logout', 'logout');
        Route::get('me', 'me');
    });
});

Route::prefix('prodi')->controller(ProdiController::class)->group(function () {
    Route::post('login', 'login');
    // Route::post('register', 'register');
    Route::middleware('auth:prodi_api')->group(function () {
        Route::post('logout', 'logout');
        Route::get('me', 'me');
    });
});

Route::prefix('mahasiswa')->controller(MahasiswaController::class)->group(function () {
    Route::post('login', 'login');
    // Route::post('register', 'register');
    Route::middleware('auth:mahasiswa_api')->group(function () {
        Route::post('logout', 'logout');
        Route::get('me', 'me');
        Route::put('update-password', 'updatePassword');
        Route::put('update-profile', 'updateProfile');
    });
});

Route::prefix('pembimbing')->controller(PembimbingController::class)->group(function () {
    Route::post('login', 'login');
    // Route::post('register', 'register');
    Route::middleware('auth:pembimbing_api')->group(function () {
        Route::post('logout', 'logout');
        Route::get('me', 'me');
    });
});