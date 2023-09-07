<?php

namespace Tests\Unit;

use App\Models\Mahasiswa;
use App\Models\Pembimbing;
use Tests\TestCase;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class PembimbingTest extends TestCase
{
    private $tokenPembimbing;

    public function setUp(): void
    {
        parent::setUp();

        $pembimbing = Pembimbing::where('username', 'anto')->first();
        $this->tokenPembimbing = JWTAuth::fromUser($pembimbing);
    }

    public function test_login_pembimbing()
    {
        $pembimbing = Pembimbing::first();
        $credentials = [
            'username' => $pembimbing->username,
            'password' => '123456789',
        ];

        $response = $this->postJson('api/pembimbing/login', $credentials);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Login berhasil',
                'role' => 'Pembimbing',
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

    public function test_logout_pembimbing()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenPembimbing];
        $response = $this->withHeaders($headers)->post('api/pembimbing/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out',
            ]);
    }

    public function test_register_pembimbing()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenPembimbing];

        $data = [
            'nama' => 'Jhon Doe',
            'nik' => '72367843',
            'username' => Str::random(8),
            'password' => "123456789"
        ];

        $response = $this->withHeaders($headers)->post('api/pembimbing/register', $data);

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
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

    public function test_show_list_nilai()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenPembimbing];
        $response = $this->withHeaders($headers)->get('api/penilaian-pembimbing/pembimbing');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Penilaian dari Pembimbing',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => [
                        'id_tempat_pkl',
                        'id_mahasiswa',
                        'nama_mahasiswa',
                        'nama_prodi',
                        'nim',
                        'nama_pembimbing',
                        'nik',
                    ]
                ]
            ]);
    }

    public function test_penilaian_pembimbing()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenPembimbing];

        $data = [
            'id_mahasiswa' => '1',
            'id_tempat_pkl' => '1',
            'integritas' => "85",
            'profesionalitas' => "85",
            'bahasa_inggris' => "85",
            'teknologi_informasi' => "85",
            'komunikasi' => "85",
            'kerja_sama' => "85",
            'organisasi' => "85",
        ];

        $response = $this->withHeaders($headers)->post('api/penilaian-pembimbing/pembimbing', $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Penilaian dari Pembimbing',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
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
                    'updated_at',
                    'created_at',
                    'id_penilaian_pembimbing'
                ]
            ]);
    }

    public function test_show_detail_nilai()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenPembimbing];
        $mahasiswa = Mahasiswa::first();
        $response = $this->withHeaders($headers)->get("api/penilaian-pembimbing/pembimbing/{$mahasiswa->id_mahasiswa}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Detail Nilai Mahasiswa',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
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
                ]
            ]);
    }

    public function test_get_list_mahasiswa()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenPembimbing];
        $response = $this->withHeaders($headers)->get("api/mahasiswa/list/pembimbing");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Daftar Mahasiswa',
            ])
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => [
                        'id_tempat_pkl',
                        'id_mahasiswa',
                        'nama_mahasiswa',
                        'nama_prodi',
                        'nim',
                        'nama_pembimbing',
                        'nik',
                    ]
                ]
            ]);
    }

    public function test_post_biodata_industri()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenPembimbing];

        $data = [
            'nama_industri' => 'Company Test',
            'nama_pimpinan' => 'John Doe',
            'alamat_kantor' => 'Jl. Test 123',
            'no_telp_fax' => '08123456789',
            'contact_person' => 'Jane Smith',
            'bidang_usaha_jasa' => 'IT Services',
            'spesialisasi_produksi_jasa' => 'Web Development',
            'kapasitas_produksi' => '1000',
            'jangkauan_pemasaran' => 'Nasional',
            'jumlah_tenaga_kerja_sd' => '10',
            'jumlah_tenaga_kerja_sltp' => '20',
            'jumlah_tenaga_kerja_slta' => '30',
            'jumlah_tenaga_kerja_smk' => '40',
            'jumlah_tenaga_kerja_smea' => '50',
            'jumlah_tenaga_kerja_smkk' => '60',
            'jumlah_tenaga_kerja_sarjana_muda' => '70',
            'jumlah_tenaga_kerja_sarjana_magister' => '80',
            'jumlah_tenaga_kerja_sarjana_doktor' => '90',
        ];

        $response = $this->withHeaders($headers)->post('api/biodata-industri/pembimbing', $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Biodata Industri Berhasil Disimpan',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id_biodata_industri',
                    'id_pembimbing',
                    'id_tempat_pkl',
                    'nama_industri',
                    'nama_pimpinan',
                    'alamat_kantor',
                    'no_telp_fax',
                    'contact_person',
                    'bidang_usaha_jasa',
                    'spesialisasi_produksi_jasa',
                    'kapasitas_produksi',
                    'jangkauan_pemasaran',
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
                ]
            ]);
    }
    
    
    public function test_get_biodata_industri()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenPembimbing];
        $response = $this->withHeaders($headers)->get("api/biodata-industri/pembimbing");

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
                    'id_pembimbing',
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
                ]
            ]);
    }

    public function test_delete_biodata_industri()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenPembimbing];

        $response = $this->withHeaders($headers)->delete('api/biodata-industri/pembimbing');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Biodata Industri Dihapus',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id_biodata_industri',
                    'id_pembimbing',
                    'id_tempat_pkl',
                    'nama_industri',
                    'nama_pimpinan',
                    'alamat_kantor',
                    'no_telp_fax',
                    'contact_person',
                    'bidang_usaha_jasa',
                    'spesialisasi_produksi_jasa',
                    'kapasitas_produksi',
                    'jangkauan_pemasaran',
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
                ]
            ]);
    }

    public function test_get_jurnal_kegiatan_mahasiswa()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenPembimbing];
        $mahasiswa = Mahasiswa::where('nim', 'D111911068')->first();
        $response = $this->withHeaders($headers)->get("api/jurnal-kegiatan/pembimbing/{$mahasiswa->id_mahasiswa}");

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

    public function test_get_daftar_hadir_mahasiswa()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenPembimbing];
        $mahasiswa = Mahasiswa::where('nim', 'D111911068')->first();
        $response = $this->withHeaders($headers)->get("api/daftar-hadir/pembimbing/{$mahasiswa->id_mahasiswa}");

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
                                'tanda-tangan',
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function test_get_catatan_khusus_mahasiswa()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenPembimbing];
        $mahasiswa = Mahasiswa::where('nim', 'D111911068')->first();
        $response = $this->withHeaders($headers)->get("api/catatan-khusus/pembimbing/{$mahasiswa->id_mahasiswa}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Catatan Khusus',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id_catatan_khusus',
                    'id_mahasiswa',
                    'id_tempat_pkl',
                    'catatan',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }
}
