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
use App\Http\Controllers\CatatanKhususController;
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

Route::get('check-token', [ProdiController::class, 'checkToken']);

Route::prefix('pengajuan-pkl')->controller(PengajuanPKLController::class)->group(function () {
    Route::middleware('auth:mahasiswa_api', 'throttle:60,1')->group(function () {
        Route::post('/mahasiswa', 'store');
        Route::get('/mahasiswa/status', 'showAllByUser');
    });

    Route::middleware('auth:akademik_api', 'throttle:60,1')->group(function () {
        Route::get('/akademik', 'index');
        Route::get('/akademik/{id}', 'show');
        Route::put('/akademik/approve-pengajuan/{id}', 'setujuiPengajuan');
        Route::put('/akademik/reject-pengajuan/{id}', 'tolakPengajuan');
    });
});

Route::prefix('tempat-pkl')->controller(TempatPKLController::class)->group(function () {
    Route::middleware('auth:mahasiswa_api', 'throttle:60,1')->group(function () {
        Route::post('/mahasiswa', 'store');
    });

    Route::middleware('auth:prodi_api', 'throttle:60,1')->group(function () {
        Route::get('/prodi', 'index');
        Route::get('/prodi/data', 'dashboardDataProdi');
        Route::get('/prodi/tahun-akademik', 'lovTahunAkademik');
    });

    Route::middleware('auth:akademik_api', 'throttle:60,1')->group(function () {
        Route::get('/akademik/data', 'dashboardDataAkademik');
    });
});

Route::prefix('lowongan-pkl')->controller(LowonganPKLController::class)->group(function () {
    Route::middleware('auth:mahasiswa_api', 'throttle:60,1')->group(function () {
        Route::get('/mahasiswa', 'index');
        Route::get('/mahasiswa/search', 'searchByKeyword');
    });

    Route::middleware('auth:prodi_api', 'throttle:60,1')->group(function () {
        Route::post('/prodi', 'store');
        Route::put('/prodi/{id}', 'update');
        Route::get('/prodi', 'showByProdi');
        Route::delete('/prodi/{id}', 'destroy');
        Route::get('/prosple', 'prosple');
        Route::get('/save-prosple', 'prospleStoreDb');
    });
});

Route::prefix('biodata-industri')->controller(BiodataIndustriController::class)->group(function () {
    Route::middleware('auth:mahasiswa_api', 'throttle:60,1')->group(function () {
        Route::get('/mahasiswa', 'index');
        Route::post('/mahasiswa/{id}', 'update');
        Route::get('/mahasiswa/detail', 'show');
    });
    
    Route::middleware('auth:prodi_api', 'throttle:60,1')->group(function () {
        Route::get('/prodi/detail/{id}', 'detailByProdi');
    });
    
    Route::middleware('auth:pembimbing_api', 'throttle:60,1')->group(function () {
        Route::get('/pembimbing', 'detailByPembimbing');
        Route::post('/pembimbing', 'store');
        Route::delete('/pembimbing', 'destroy');
    });
});

Route::prefix('jurnal-kegiatan')->controller(JurnalKegiatanController::class)->group(function () {
    Route::middleware('auth:mahasiswa_api', 'throttle:60,1')->group(function () {
        Route::post('/mahasiswa', 'store');
        Route::get('/mahasiswa', 'indexByUser');
        Route::put('/mahasiswa/{id}', 'update');
        Route::delete('/mahasiswa/{id}', 'destroy');
    });

    Route::middleware('auth:prodi_api', 'throttle:60,1')->group(function () {
        Route::get('/prodi/{id}', 'indexByProdi');
    });

    Route::middleware('auth:pembimbing_api', 'throttle:60,1')->group(function () {
        Route::get('/pembimbing', 'indexByPembimbing');
        Route::get('/pembimbing/{id}', 'showByPembimbing');
    });
});

Route::prefix('daftar-hadir')->controller(DaftarHadirController::class)->group(function () {
    Route::middleware('auth:mahasiswa_api', 'throttle:60,1')->group(function () {
        Route::post('/mahasiswa', 'store');
        Route::get('/mahasiswa', 'index');
        Route::put('/mahasiswa/{id}', 'update');
        Route::delete('/mahasiswa/{id}', 'destroy');
    });

    Route::middleware('auth:prodi_api', 'throttle:60,1')->group(function () {
        Route::get('/prodi/{id}', 'showByProdi');
    });

    Route::middleware('auth:pembimbing_api', 'throttle:60,1')->group(function () {
        Route::get('/pembimbing', 'indexByPembimbing');
        Route::get('/pembimbing/{id}', 'showByPembimbing');
    });
});

Route::prefix('penilaian-prodi')->controller(PenilaianProdiController::class)->group(function () {
    Route::middleware('auth:mahasiswa_api', 'throttle:60,1')->group(function () {
        Route::get('/mahasiswa', 'showByUser');
    });

    Route::middleware('auth:prodi_api', 'throttle:60,1')->group(function () {
        Route::post('/prodi', 'store');
        Route::get('/prodi', 'index');
        Route::get('/prodi/{id}', 'show');
        Route::delete('/prodi/{id}', 'destroy');
    });
});

Route::prefix('penilaian')->controller(PenilaianController::class)->group(function () {
    Route::middleware('auth:mahasiswa_api', 'throttle:60,1')->group(function () {
        Route::get('/mahasiswa', 'index');
    });
});

Route::prefix('penilaian-pembimbing')->controller(PenilaianPembimbingController::class)->group(function () {
    Route::middleware('auth:pembimbing_api', 'throttle:60,1')->group(function () {
        Route::post('/pembimbing', 'store');
        Route::get('/pembimbing', 'index');
        Route::get('/pembimbing/{id}', 'show');
        Route::delete('/pembimbing/{id}', 'destroy');
    });
});

Route::prefix('catatan-khusus')->controller(CatatanKhususController::class)->group(function () {
    Route::middleware('auth:mahasiswa_api', 'throttle:60,1')->group(function () {
        Route::get('/mahasiswa', 'show');
        Route::post('/mahasiswa', 'store');
        Route::delete('/mahasiswa', 'destroy');
    });

    Route::middleware('auth:prodi_api', 'throttle:60,1')->group(function () {
        Route::get('/prodi/{id}', 'showByProdi');
    });

    Route::middleware('auth:pembimbing_api', 'throttle:60,1')->group(function () {
        Route::get('/pembimbing/{id}', 'showByPembimbing');
    });
});

Route::prefix('laporan')->controller(LaporanPKLController::class)->group(function () {
    Route::middleware('auth:mahasiswa_api', 'throttle:60,1')->group(function () {
        Route::get('/mahasiswa', 'indexByMahasiswa');
        Route::post('/mahasiswa/upload', 'uploadLaporan');
        Route::delete('/mahasiswa/{id}', 'cancel');
    });

    Route::middleware('auth:prodi_api', 'throttle:60,1')->group(function () {
        Route::get('/prodi', 'index');
        Route::get('/prodi/{id}', 'detailByProdi');
    });
});

Route::prefix('akademik')->controller(AkademikController::class)->group(function () {
    Route::post('login', 'login');
    Route::middleware('auth:akademik_api', 'throttle:60,1')->group(function () {
        Route::post('logout', 'logout');
        Route::get('me', 'me');
    });
    Route::put('save-player-id', 'savePlayerId');
});

Route::prefix('prodi')->controller(ProdiController::class)->group(function () {
    Route::post('login', 'login');
    Route::middleware('auth:prodi_api', 'throttle:60,1')->group(function () {
        Route::post('logout', 'logout');
        Route::get('me', 'me');
        Route::get('/list-by-prodi', 'indexByProdi');
    });

    Route::get('/list', 'index');
});

Route::prefix('mahasiswa')->controller(MahasiswaController::class)->group(function () {
    Route::post('login', 'login');
    Route::middleware('auth:mahasiswa_api', 'throttle:60,1')->group(function () {
        Route::post('logout', 'logout');
        Route::get('me', 'me');
        Route::get('status', 'checkStatus');
        Route::put('update-password', 'updatePassword');
        Route::put('update-profile', 'updateProfile');
        Route::put('save-player-id', 'savePlayerId');
    });

    Route::middleware('auth:akademik_api', 'throttle:60,1')->group(function () {
        Route::get('/list', 'index');
        Route::post('add', 'register');
        Route::put('update/{id}', 'update');
        Route::delete('delete/{id}', 'destroy');
    });

    Route::middleware('auth:prodi_api', 'throttle:60,1')->group(function () {
        Route::get('/list/prodi', 'listByProdi');
        Route::post('add/prodi', 'register');
        Route::put('update/prodi/{id}', 'update');
        Route::delete('delete/prodi/{id}', 'destroy');
    });

    Route::middleware('auth:pembimbing_api', 'throttle:60,1')->group(function () {
        Route::get('/list/pembimbing', 'listByPembimbing');
    });
    Route::get('/tahun_masuk', 'lovTahunMasuk');
});

Route::prefix('pembimbing')->controller(PembimbingController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
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

    Route::middleware('auth:prodi_api', 'throttle:60,1')->group(function () {
        Route::get('/list/prodi', 'index');
        Route::post('add/prodi', 'register');
        Route::put('update/prodi/{id}', 'update');
        Route::delete('delete/prodi/{id}', 'destroy');
    });

    Route::get('/list-pembimbing', 'index');
});
