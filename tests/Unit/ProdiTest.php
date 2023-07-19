<?php

namespace Tests\Unit;

use App\Models\BiodataIndustri;
use App\Models\JurnalKegiatan;
use App\Models\LowonganPKL;
use App\Models\Mahasiswa;
use App\Models\Pembimbing;
use App\Models\Prodi;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;


class ProdiTest extends TestCase
{
    private $tokenProdi;

    public function setUp(): void
    {
        parent::setUp();

        $prodi = Prodi::first();
        $this->tokenProdi = JWTAuth::fromUser($prodi);
    }

    public function test_login_Prodi()
    {
        $prodi = Prodi::first();
        $credentials = [
            'username' => $prodi->username,
            'password' => 'prodiiftedc',
        ];

        $response = $this->postJson('api/prodi/login', $credentials);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Login berhasil',
                'role' => 'Prodi',
                'access_token' => true,
                'token_type' => 'bearer',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'role',
                'access_token',
                'token_type',
                'expires_at',
            ]);
    }

    public function test_logout_akademik()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenProdi];
        $response = $this->withHeaders($headers)->post('api/prodi/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out',
            ]);
    }

    public function test_get_list_mahasiswa_by_prodi()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenProdi];
        $response = $this->withHeaders($headers)->get("api/mahasiswa/list/prodi");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Semua Data Mahasiswa Politeknik TEDC Bandung',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => [
                        'id_mahasiswa',
                        'nama',
                        'nim',
                        'nama_prodi',
                        'id_prodi',
                        'semester',
                        'email',
                        'username',
                        'nomor_hp',
                    ]
                ]
            ]);
    }

    public function test_post_data_mahasiswa_by_prodi()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenProdi];

        $data = [
            'username' => 'test',
            'nama' => 'Name Test',
            'nim' => "D1191922",
            'prodi' => "1",
            'semester' => "8 (Delapan)",
            'email' => "",
            'nomor_hp' => "08566567567",
            'password' => "123456789",
        ];

        $response = $this->withHeaders($headers)->post("api/mahasiswa/add/prodi", $data);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Berhasil Menambah Data Mahasiswa',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'user' => [
                    'username',
                    'nama',
                    'nim',
                    'prodi',
                    'semester',
                    'email',
                    'nomor_hp',
                    'updated_at',
                    'created_at',
                    'id_mahasiswa'
                ]
            ]);
    }

    public function test_put_data_mahasiswa_by_prodi()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenProdi];

        $data = [
            'username' => 'test',
            'nama' => 'Name Test',
            'nim' => "D1191922",
            'prodi' => "1",
            'semester' => "8 (Delapan)",
            'email' => "",
            'nomor_hp' => "08566567567",
            'password' => "123456789",
        ];

        $mahasiswa = Mahasiswa::latest()->first();

        $response = $this->withHeaders($headers)->put("api/mahasiswa/update/prodi/{$mahasiswa->id_mahasiswa}", $data);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Berhasil Memperbarui Data Mahasiswa',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'user' => [
                    'username',
                    'nama',
                    'nim',
                    'prodi',
                    'semester',
                    'email',
                    'nomor_hp',
                    'updated_at',
                    'created_at',
                    'id_mahasiswa'
                ]
            ]);
    }

    public function test_delete_data_mahasiswa_by_prodi()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenProdi];

        $mahasiswa = Mahasiswa::latest()->first();

        $response = $this->withHeaders($headers)->delete("api/mahasiswa/delete/prodi/{$mahasiswa->id_mahasiswa}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Data Mahasiswa Dihapus',
            ])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id_mahasiswa',
                    'username',
                    'nama',
                    'nim',
                    'prodi',
                    'semester',
                    'email',
                    'nomor_hp',
                    'updated_at',
                    'created_at',
                ]
            ]);
    }

    public function test_get_list_all_pembimbing()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenProdi];
        $response = $this->withHeaders($headers)->get("api/pembimbing/list/prodi");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Semua Data Pembimbing PKL',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => [
                        'id_pembimbing',
                        'username',
                        'nama',
                        'nik',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    public function test_post_data_pembimbing()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenProdi];

        $data = [
            'username' => 'tests',
            'nama' => 'Name Test',
            'nik' => "376798423",
            'password' => "123456789",
        ];

        $response = $this->withHeaders($headers)->post("api/pembimbing/add/prodi", $data);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Berhasil Menambah Data Pembimbing',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'user' => [
                    'username',
                    'nama',
                    'nik',
                    'updated_at',
                    'created_at',
                    'id_pembimbing'
                ]
            ]);
    }

    public function test_put_data_pembimbing()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenProdi];

        $data = [
            'username' => 'testuiu',
            'nama' => 'Name Test update',
            'nim' => "D1191922",
            'prodi' => "1",
            'semester' => "8 (Delapan)",
            'email' => "",
            'nomor_hp' => "08566567567",
            'password' => "123456789",
        ];

        $pembimbing = Pembimbing::latest()->first();

        $response = $this->withHeaders($headers)->put("api/pembimbing/update/prodi/{$pembimbing->id_pembimbing}", $data);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Berhasil Memperbarui Data Pembimbing',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'user' => [
                    'id_pembimbing',
                    'username',
                    'nama',
                    'nik',
                    'updated_at',
                    'created_at',
                ]
            ]);
    }

    public function test_delete_data_pembimbing()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenProdi];

        $pembimbing = Pembimbing::latest()->first();

        $response = $this->withHeaders($headers)->delete("api/pembimbing/delete/prodi/{$pembimbing->id_pembimbing}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Data Pembimbing Dihapus',
            ])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id_pembimbing',
                    'username',
                    'nama',
                    'nik',
                    'updated_at',
                    'created_at',
                ]
            ]);
    }

    public function test_get_list_tempat_pkl()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenProdi];

        $response = $this->withHeaders($headers)->get('api/tempat-pkl/prodi');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Semua Data Konfirmasi Mahasiswa Diterima PKL',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => [
                        'id_tempat_pkl',
                        'id_pengajuan',
                        'nama_mahasiswa',
                        'nama_prodi',
                        'nim',
                        'id_biodata_industri',
                        'nama_pembimbing',
                        'nik',
                        'id_mahasiswa',
                        'nama_perusahaan',
                        'alamat_perusahaan',
                        'tanggal_mulai',
                        'tanggal_selesai',
                        'status',
                        'surat',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ]);
    }

    public function test_get_list_data_lowongan_pkl()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenProdi];

        $response = $this->withHeaders($headers)->get('api/lowongan-pkl/prodi');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Data Lowongan PKL',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => [
                        'id_lowongan',
                        'id_prodi',
                        'posisi',
                        'nama_perusahaan',
                        'alamat_perusahaan',
                        'gambar',
                        'url',
                        'sumber',
                    ]
                ]
            ]);
    }

    public function test_post_data_lowongan_pkl()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenProdi];

        $file = UploadedFile::fake()->image('gambar.png');

        $data = [
            'posisi' => 'test',
            'nama_perusahaan' => 'test',
            'alamat_perusahaan' => "D1191922",
            'gambar' => $file,
        ];


        $response = $this->withHeaders($headers)->post('api/lowongan-pkl/prodi', $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Data Lowongan PKL',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id_lowongan',
                    'id_prodi',
                    'posisi',
                    'nama_perusahaan',
                    'alamat_perusahaan',
                    'gambar',
                    'url',
                    'sumber',
                ]
            ]);
    }

    public function test_put_data_lowongan_pkl()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenProdi];

        $file = UploadedFile::fake()->image('gambar.png');
        $lowonganPkl = LowonganPKL::latest()->first();
        $data = [
            'posisi' => 'test update',
            'nama_perusahaan' => 'test',
            'alamat_perusahaan' => "D1191922",
            'gambar' => $file,
        ];


        $response = $this->withHeaders($headers)->put("api/lowongan-pkl/prodi/{$lowonganPkl->id_lowongan}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Data Lowongan PKL',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id_lowongan',
                    'id_prodi',
                    'posisi',
                    'nama_perusahaan',
                    'alamat_perusahaan',
                    'gambar',
                    'url',
                    'sumber',
                ]
            ]);
    }

    public function test_delete_data_lowongan_pkl()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenProdi];

        $lowonganPkl = LowonganPKL::latest()->first();

        $response = $this->withHeaders($headers)->delete("api/lowongan-pkl/prodi/{$lowonganPkl->id_lowongan}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Lowongan PKL Terhapus',
            ])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id_lowongan',
                    'id_prodi',
                    'posisi',
                    'nama_perusahaan',
                    'alamat_perusahaan',
                    'gambar',
                    'url',
                    'sumber',
                ]
            ]);
    }

    public function test_scrapping_data_lowongan_pkl()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenProdi];

        $response = $this->withHeaders($headers)->get('api/lowongan-pkl/prosple');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Scraping data dari prosple',
                'status' => 'Success',
            ])
            ->assertJsonStructure([
                'message',
                'status',
                'data' => [
                    '*' => [
                        'posisi',
                        'nama_perusahaan',
                        'alamat_perusahaan',
                        'gambar',
                        'url',
                        'sumber',
                    ]
                ]
            ]);
    }

    public function test_get_biodata_industri_by_prodi()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenProdi];
        $biodataIndustri = BiodataIndustri::latest()->first();

        $response = $this->withHeaders($headers)->get("api/biodata-industri/prodi/detail/{$biodataIndustri->id_biodata_industri}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Biodata Industri',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id_biodata_industri',
                    'id_mahasiswa',
                    'id_tempat_pkl',
                    'nama_industri',
                    'nama_pimpinan',
                    'alamat_kantor',
                    'no_telp_fax',
                    'contact_person',
                    'bidang_usaha_jasa',
                    'spesialisasi_produksi_jasa',
                    'jangkauan_pemasaran',
                    'kapasitas_produksi',
                    'jumlah_tenaga_kerja_sd',
                    'jumlah_tenaga_kerja_sltp',
                    'jumlah_tenaga_kerja_slta',
                    'jumlah_tenaga_kerja_smk',
                    'jumlah_tenaga_kerja_smea',
                    'jumlah_tenaga_kerja_smkk',
                    'jumlah_tenaga_kerja_sarjana_muda',
                    'jumlah_tenaga_kerja_sarjana_magister',
                    'jumlah_tenaga_kerja_sarjana_doktor',
                    'created_at',
                    'updated_at',
                    'nama',
                    'nim',
                ]
            ]);
    }

    public function test_get_jurnal_kegiatan_by_prodi()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenProdi];
        $mahasiswa = Mahasiswa::first();

        $response = $this->withHeaders($headers)->get("api/jurnal-kegiatan/prodi/{$mahasiswa->id_mahasiswa}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Jurnal Kegiatan',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    [
                        'minggu',
                        'data_kegiatan' => [
                            [
                                'id_jurnal_kegiatan',
                                'id_mahasiswa',
                                'tanggal',
                                'minggu',
                                'bidang_pekerjaan',
                                'keterangan',
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function test_get_daftar_hadir_by_prodi()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenProdi];
        $mahasiswa = Mahasiswa::first();

        $response = $this->withHeaders($headers)->get("api/daftar-hadir/prodi/{$mahasiswa->id_mahasiswa}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Daftar Hadir',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    [
                        'minggu',
                        'data_kehadiran' => [
                            [
                                'id_daftar_hadir',
                                'id_mahasiswa',
                                'hari_tanggal',
                                'minggu',
                                'tanda_tangan',
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function test_get_laporan_by_prodi()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenProdi];
        $mahasiswa = Mahasiswa::first();

        $response = $this->withHeaders($headers)->get("api/laporan/prodi/{$mahasiswa->id_mahasiswa}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Detail Laporan PKL',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id_laporan',
                    'id_mahasiswa',
                    'nama',
                    'nim',
                    'nama_prodi',
                    'file',
                    'tanggal_upload'
                ]
            ]);
    }

    public function test_penilaian_prodi()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenProdi];
        $data = [
            'id_mahasiswa' => 1,
            'id_tempat_pkl' => 1,
            'presentasi' => 87.45,
            'dokumen' => 98.58,
        ];

        $response = $this->withHeaders($headers)->post('api/penilaian-prodi/prodi', $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Penilaian dari Prodi',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id_penilaian_prodi',
                    'id_mahasiswa',
                    'id_tempat_pkl',
                    'presentasi',
                    'dokumen',
                    'total_nilai',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    public function test_get_list_nilai_pkl()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenProdi];

        $response = $this->withHeaders($headers)->get('api/penilaian-prodi/prodi');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Penilaian dari Prodi',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    [
                        'id_penilaian_prodi',
                        'id_mahasiswa',
                        'id_tempat_pkl',
                        'presentasi',
                        'dokumen',
                        'total_nilai',
                        'created_at',
                        'updated_at',
                        'nama',
                        'nama_prodi',
                        'nim',
                    ]
                ]
            ]);
    }

    public function test_get_detail_nilai_pkl()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenProdi];
        $mahasiswa = Mahasiswa::first();
        $response = $this->withHeaders($headers)->get("api/penilaian-prodi/prodi/{$mahasiswa->id_mahasiswa}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Detail Nilai Mahasiswa',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'nama',
                    'nama_prodi',
                    'nim',
                    'id_penilaian_prodi',
                    'presentasi',
                    'dokumen',
                    'id_penilaian_pembimbing',
                    'id_mahasiswa',
                    'id_tempat_pkl',
                    'integritas',
                    'profesionalitas',
                    'bahasa_inggris',
                    'teknologi_informasi',
                    'komunikasi',
                    'kerja_sama',
                    'organisasi',
                    'total_nilai',
                    'created_at',
                    'updated_at',
                    'nilai_akhir',
                    'nilai_huruf'
                ]
            ]);
    }
}
