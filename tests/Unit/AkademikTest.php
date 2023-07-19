<?php

namespace Tests\Unit;

use App\Models\Akademik;
use App\Models\Mahasiswa;
use App\Models\Pembimbing;
use App\Models\PengajuanPKL;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;


class AkademikTest extends TestCase
{
    private $tokenAkademik;

    public function setUp(): void
    {
        parent::setUp();

        $akademik = Akademik::first();
        $this->tokenAkademik = JWTAuth::fromUser($akademik);
    }

    public function test_login_akademik()
    {
        $akademik = Akademik::first();
        $credentials = [
            'username' => $akademik->username,
            'password' => 'akademiktedc1',
        ];

        $response = $this->postJson('api/akademik/login', $credentials);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Login berhasil',
                'role' => 'Akademik',
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
        $headers = ['Authorization' => 'Bearer ' . $this->tokenAkademik];
        $response = $this->withHeaders($headers)->post('api/akademik/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out',
            ]);
    }

    public function test_get_data_pengajuan_pkl()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenAkademik];
        $response = $this->withHeaders($headers)->get('api/pengajuan-pkl/akademik');

        $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'Semua Data Pengajuan PKL',
        ])
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                '*' => [
                    'id_pengajuan',
                    'id_mahasiswa',
                    'nama',
                    'nim',
                    'nama_prodi',
                    'nama_perusahaan',
                    'alamat_perusahaan',
                    'tanggal_mulai',
                    'tanggal_selesai',
                    'status',
                    'surat',
                    'created_at',
                    'updated_at'
                ]
            ]
        ]);
    }

    public function test_get_detail_pengajuan_pkl()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenAkademik];
        $pengajuanPkl = PengajuanPKL::first(); 
        $response = $this->withHeaders($headers)->get("api/pengajuan-pkl/akademik/{$pengajuanPkl->id_pengajuan}");

        $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => "Detail Pengajuan PKL id {$pengajuanPkl->id_pengajuan}",
        ])
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id_pengajuan',
                'id_mahasiswa',
                'nama_perusahaan',
                'alamat_perusahaan',
                'tanggal_mulai',
                'tanggal_selesai',
                'status',
                'surat',
                'created_at',
                'updated_at',
                'nama',
                'nim',
                'nama_prodi',
            ]
        ]);
    }

    public function test_setujui_pengajuan_pkl()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenAkademik];
        $pengajuanPkl = PengajuanPKL::first(); 
        $response = $this->withHeaders($headers)->put("api/pengajuan-pkl/akademik/approve-pengajuan/{$pengajuanPkl->id_pengajuan}");

        $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'Pengajuan PKL berhasil disetujui',
        ])
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id_pengajuan',
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
        ]);
    }

    public function test_tolak_pengajuan_pkl()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenAkademik];
        $pengajuanPkl = PengajuanPKL::first(); 
        $response = $this->withHeaders($headers)->put("api/pengajuan-pkl/akademik/reject-pengajuan/{$pengajuanPkl->id_pengajuan}");

        $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'Pengajuan pkl berhasil ditolak',
        ])
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id_pengajuan',
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
        ]);
    }

    public function test_get_list_all_mahasiswa()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenAkademik];
        $response = $this->withHeaders($headers)->get("api/mahasiswa/list");

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

    public function test_post_data_mahasiswa()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenAkademik];

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

        $response = $this->withHeaders($headers)->post("api/mahasiswa/add", $data);

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

    public function test_put_data_mahasiswa()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenAkademik];

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

        $response = $this->withHeaders($headers)->put("api/mahasiswa/update/{$mahasiswa->id_mahasiswa}", $data);

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

    public function test_delete_data_mahasiswa()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenAkademik];

        $mahasiswa = Mahasiswa::latest()->first();

        $response = $this->withHeaders($headers)->delete("api/mahasiswa/delete/{$mahasiswa->id_mahasiswa}");

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
        $headers = ['Authorization' => 'Bearer ' . $this->tokenAkademik];
        $response = $this->withHeaders($headers)->get("api/pembimbing/list");

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
        $headers = ['Authorization' => 'Bearer ' . $this->tokenAkademik];

        $data = [
            'username' => 'test',
            'nama' => 'Name Test',
            'nik' => "376798423",
            'password' => "123456789",
        ];

        $response = $this->withHeaders($headers)->post("api/pembimbing/add", $data);

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
        $headers = ['Authorization' => 'Bearer ' . $this->tokenAkademik];

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

        $response = $this->withHeaders($headers)->put("api/pembimbing/update/{$pembimbing->id_pembimbing}", $data);

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
        $headers = ['Authorization' => 'Bearer ' . $this->tokenAkademik];

        $pembimbing = Pembimbing::latest()->first();

        $response = $this->withHeaders($headers)->delete("api/pembimbing/delete/{$pembimbing->id_pembimbing}");

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

}


