<?php

use App\Http\Controllers\AkademikController;
use App\Http\Controllers\BiodataIndustriController;
use App\Http\Controllers\DaftarHadirController;
use App\Http\Controllers\JurnalKegiatanController;
use App\Http\Controllers\LaporanPKLController;
use App\Http\Controllers\LowonganPKLController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PembimbingController;
use App\Http\Controllers\PengajuanPKLController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\PenilaianPembimbingController;
use App\Http\Controllers\PenilaianProdiController;
use App\Http\Controllers\TempatPKLController;
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

Route::prefix('pengajuan-pkl')->controller(PengajuanPKLController::class)->group(function () {
    Route::middleware('auth:mahasiswa_api', 'throttle:60,1')->group(function () {
        Route::post('/', 'store');
        Route::get('status/', 'showAllByUser');
        // Route::post('update/{id}', 'update');
        // Route::delete('/{id}', 'delete');
    });

    Route::middleware('auth:akademik_api', 'throttle:60,1')->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
        Route::put('approve-pengajuan/{id}', 'setujuiPengajuan');
        Route::put('reject-pengajuan/{id}', 'tolakPengajuan');
    });
});

Route::prefix('tempat-pkl')->controller(TempatPKLController::class)->group(function () {
    Route::middleware('auth:mahasiswa_api', 'throttle:60,1')->group(function () {
        Route::post('/', 'store');
    });

    Route::middleware('auth:prodi_api', 'throttle:60,1')->group(function () {
        Route::get('/', 'index');
    });
});

Route::prefix('lowongan-pkl')->controller(LowonganPKLController::class)->group(function () {
    Route::middleware('auth:mahasiswa_api', 'throttle:60,1')->group(function () {
        Route::get('/', 'index');
        Route::get('/search', 'searchByKeyword');
    });

    Route::middleware('auth:prodi_api', 'throttle:60,1')->group(function () {
        Route::post('/', 'store');
        Route::put('/{id}', 'update');
        Route::get('/prodi', 'showByProdi');
        Route::delete('/{id}', 'destroy');
        Route::get('/prosple', 'prosple');
        Route::get('/save-prosple', 'prospleStoreDb');
        Route::get('/glints', 'glints');
    });
});

Route::prefix('biodata-industri')->controller(BiodataIndustriController::class)->group(function () {
    Route::middleware('auth:mahasiswa_api', 'throttle:60,1')->group(function () {
        Route::post('/', 'store');
        Route::get('/', 'index');
        Route::get('/detail-user', 'detailByUser');
        Route::post('/{id}', 'update');
        Route::get('/{id}', 'show');
        Route::delete('/{id}', 'destroy');
    });

    Route::middleware('auth:pembimbing_api', 'throttle:60,1')->group(function () {
        Route::get('/', 'showByPembimbing');
        Route::get('/detail/{id}', 'detailByPembimbing');
    });

    Route::middleware('auth:prodi_api', 'throttle:60,1')->group(function () {
        Route::get('/', 'showByProdi');
        Route::get('/detail/{id}', 'detailByProdi');
    });
});

Route::prefix('jurnal-kegiatan')->controller(JurnalKegiatanController::class)->group(function () {
    Route::middleware('auth:mahasiswa_api', 'throttle:60,1')->group(function () {
        Route::post('/', 'store');
        Route::get('/index-user', 'indexByUser');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });

    Route::middleware('auth:prodi_api', 'throttle:60,1')->group(function () {
        Route::get('/', 'showByProdi');
    });
});

Route::prefix('daftar-hadir')->controller(DaftarHadirController::class)->group(function () {
    Route::middleware('auth:mahasiswa_api', 'throttle:60,1')->group(function () {
        Route::post('/', 'store');
        Route::get('/', 'index');
        Route::put('/{id}', 'update');
        // Route::get('/{id}', 'show');
        Route::delete('/{id}', 'destroy');
    });
});

Route::prefix('penilaian-prodi')->controller(PenilaianProdiController::class)->group(function () {
    Route::middleware('auth:prodi_api', 'throttle:60,1')->group(function () {
        Route::post('/', 'store');
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
        Route::delete('/{id}', 'destroy');
    });

    Route::middleware('auth:mahasiswa_api', 'throttle:60,1')->group(function () {
        Route::get('/show-user', 'showByUser');
    });
});

Route::prefix('penilaian')->controller(PenilaianController::class)->group(function () {
    Route::middleware('auth:mahasiswa_api', 'throttle:60,1')->group(function () {
        Route::get('/', 'index');
    });
});

Route::prefix('penilaian-pembimbing')->controller(PenilaianPembimbingController::class)->group(function () {
    Route::middleware('auth:pembimbing_api', 'throttle:60,1')->group(function () {
        Route::post('/', 'store');
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
        Route::delete('/{id}', 'destroy');
    });
});

Route::prefix('upload-laporan')->controller(LaporanPKLController::class)->group(function () {
    Route::middleware('auth:mahasiswa_api', 'throttle:60,1')->group(function () {
        Route::post('/', 'uploadLaporan');
        Route::delete('/{id}', 'cancel');
    });
    
    Route::middleware('auth:prodi_api', 'throttle:60,1')->group(function () {
        Route::get('/', 'index');
    });
});

Route::prefix('akademik')->controller(AkademikController::class)->group(function () {
    Route::post('login', 'login');
    Route::middleware('auth:akademik_api', 'throttle:60,1')->group(function () {
        Route::post('logout', 'logout');
        Route::get('me', 'me');
    });
});

Route::prefix('prodi')->controller(ProdiController::class)->group(function () {
    Route::post('login', 'login');
    Route::middleware('auth:prodi_api', 'throttle:60,1')->group(function () {
        Route::post('logout', 'logout');
        Route::get('me', 'me');
    });
});

Route::prefix('mahasiswa')->controller(MahasiswaController::class)->group(function () {
    Route::post('login', 'login');
    Route::middleware('auth:mahasiswa_api', 'throttle:60,1')->group(function () {
        Route::post('logout', 'logout');
        Route::get('me', 'me');
        Route::put('update-password', 'updatePassword');
        Route::put('update-profile', 'updateProfile');
    });

    Route::middleware('auth:akademik_api', 'throttle:60,1')->group(function () {
        Route::get('/list', 'index');
        Route::post('add', 'register');
        Route::put('update/{id}', 'update');
        Route::delete('delete/{id}', 'destroy');
    });
});

Route::prefix('pembimbing')->controller(PembimbingController::class)->group(function () {
    Route::post('login', 'login');
    Route::middleware('auth:pembimbing_api', 'throttle:60,1')->group(function () {
        Route::post('logout', 'logout');
        Route::get('me', 'me');
    });

    Route::middleware('auth:akademik_api', 'throttle:60,1')->group(function () {
        Route::get('/list', 'index');
        Route::post('add', 'register');
        Route::put('update/{id}', 'update');
        Route::delete('delete/{id}', 'destroy');
    });
});