<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\TempatPKL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class TempatPKLController extends Controller
{
    public function index()
    {
        $tempat_pkl = DB::table('tempat_pkl')
            ->join('pengajuan_pkl', 'tempat_pkl.id_pengajuan', '=', 'pengajuan_pkl.id_pengajuan')
            ->join('mahasiswa', 'pengajuan_pkl.id_mahasiswa', '=', 'mahasiswa.id_mahasiswa')
            ->leftJoin('pembimbing', 'tempat_pkl.id_pembimbing', '=', 'pembimbing.id_pembimbing')
            ->join('prodi', 'mahasiswa.prodi', '=', 'prodi.id_prodi')
            ->select('tempat_pkl.*', 'mahasiswa.nama as nama_mahasiswa', 'prodi.nama_prodi', 'prodi.id_prodi', 'mahasiswa.nim', 'pembimbing.nama as nama_pembimbing', 'pembimbing.nik', 'pengajuan_pkl.*', 'tempat_pkl.created_at as tahun_pkl')
            ->where('mahasiswa.prodi', '=', Auth::user()->id_prodi)
            ->get();

            $tempatPklData = [];
            foreach ($tempat_pkl as $data) {
                $tahun_akademik = $this->getTahunAkademik($data->tahun_pkl);
                $tempatPklData[] = [
                    "id_tempat_pkl" => $data->id_tempat_pkl,
                    "id_pengajuan" => $data->id_pengajuan,
                    "nama_mahasiswa" => $data->nama_mahasiswa,
                    "nama_prodi" => $data->nama_prodi,
                    "nim" => $data->nim,
                    "id_prodi" => $data->id_prodi,
                    "tahun_akademik" => $tahun_akademik,
                    "nama_pembimbing" => $data->nama_pembimbing,
                    "nik" => $data->nik,
                    "id_pembimbing" => $data->id_pembimbing,
                    "id_mahasiswa" => $data->id_mahasiswa,
                    "nama_perusahaan" => $data->nama_perusahaan,
                    "alamat_perusahaan" => $data->alamat_perusahaan,
                    "ditujukan" => $data->ditujukan,
                    "tanggal_mulai" => $data->tanggal_mulai,
                    "tanggal_selesai" => $data->tanggal_selesai,
                    "status" => $data->status,
                    "surat" => $data->surat,
                    "created_at" => $data->created_at,
                    "updated_at" => $data->updated_at,
                ];
            }
        return response()->json([
            'status' => 'success',
            'message' => 'Semua Data Konfirmasi Mahasiswa Diterima PKL',
            'data' => $tempatPklData,
        ], 200);
    }

    function getTahunAkademik($tahunPKLTimestamp)
    {
        // Konversi timestamp string menjadi integer
        $tahunPKL = strtotime($tahunPKLTimestamp);
    
        if ($tahunPKL === false) {
            return "Format timestamp tidak valid";
        }
    
        // Ambil bulan dari tanggal mahasiswa mulai PKL
        $bulanPKL = date("n", $tahunPKL);
        
        // Jika bulan masuk lebih besar atau sama dengan 9 (September),
        // maka tahun akademik dimulai pada tahun tahunMasuk,
        // jika tidak, maka tahun akademik dimulai pada tahun sebelumnya
        $tahunAkademikAwal = ($bulanPKL >= 9) ? date("Y", $tahunPKL) : (date("Y", $tahunPKL) - 1);
    
        // Tahun akademik berakhir selalu 1 tahun setelah tahun akademik dimulai
        $tahunAkademikAkhir = $tahunAkademikAwal + 1;
    
        $tahunAkademik = $tahunAkademikAwal . '/' . $tahunAkademikAkhir;
    
        return $tahunAkademik;
    }
    
    public function dashboardDataProdi() {
        $tahunAkademik = $this->getTahunAkademik(date("Y"));
        $totalMahasiswa = Mahasiswa::where('prodi', Auth::user()->id_prodi)->count();
        $totalMahasiswaTelahPKL = TempatPKL::all()->count();

        $totalMahasiswaSedangPKL = TempatPKL::whereYear('created_at', '=', explode('/', $tahunAkademik)[0])->count();

        return response()->json([
            'status' => 'success',
            'message' => 'Data Dashboard Prodi',
            'data' => [
                "total_mahasiswa" =>  $totalMahasiswa,
                "tahun_akademik" => $tahunAkademik,
                "mahasiswa_telah_pkl" => $totalMahasiswaTelahPKL,
                "mahasiwa_belum_pkl" => $totalMahasiswa - $totalMahasiswaTelahPKL,
                "mahasiswa_sedang_pkl" => $totalMahasiswaSedangPKL
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_pengajuan' => 'required',
            'id_pembimbing' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $tempat_pkl = TempatPKL::create([
            'id_pengajuan' => $request->id_pengajuan,
            'id_pembimbing' => $request->id_pembimbing,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil konfirmasi diterima pkl',
            'data' => $tempat_pkl,
        ], 200);
    }
}
