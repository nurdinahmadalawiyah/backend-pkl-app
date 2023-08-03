<?php

namespace Tests\Unit;

use App\Models\DaftarHadir;
use App\Models\JurnalKegiatan;
use App\Models\LaporanPKL;
use App\Models\Mahasiswa;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class MahasiswaTest extends TestCase
{
    private $tokenMahasiswa;

    public function setUp(): void
    {
        parent::setUp();

        $mahasiswa = Mahasiswa::first();
        $this->tokenMahasiswa = JWTAuth::fromUser($mahasiswa);
    }

    public function test_login_mahasiswa()
    {
        $mahasiswa = Mahasiswa::first();
        $credentials = [
            'username' => $mahasiswa->username,
            'password' => 'D111911004',
        ];

        $response = $this->postJson('api/mahasiswa/login', $credentials);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Login berhasil',
                'role' => 'Mahasiswa',
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

    public function test_logout_mahasiswa()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];
        $response = $this->withHeaders($headers)->post('api/mahasiswa/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out',
            ]);
    }

    public function test_profile_mahasiswa()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];
        $response = $this->withHeaders($headers)->get('api/mahasiswa/me');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Profile',
            ])
            ->assertJsonStructure([
                'status',
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
                    'created_at',
                    'updated_at',
                    'nama_prodi'
                ]
            ]);
    }

    public function test_change_password()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];

        $data = [
            'password_lama' => 'D111911004',
            'password_baru' => 'D111911004',
        ];

        $response = $this->withHeaders($headers)->put('api/mahasiswa/update-password/', $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Password berhasil diubah',
            ]);
    }

    public function test_update_profile()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];

        $data = [
            'email' => 'ahmadyusuf@mail.com',
            'username' => 'D111911004',
            'semester' => '8 (Delapan)',
            'nomor_hp' => '08965367282'
        ];

        $response = $this->withHeaders($headers)->put('api/mahasiswa/update-profile/', $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Profile updated successfully.',
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
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    public function test_check_status()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];
        $response = $this->withHeaders($headers)->get('api/mahasiswa/status');
        $mahasiswa = Mahasiswa::first();

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Status Mahasiswa '. $mahasiswa->nama,
            ])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id_mahasiswa',
                    'status',
                    'telah_konfirmasi'
                ]
            ]);
    }

    public function test_list_lowongan_pkl()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];
        $response = $this->withHeaders($headers)->get('api/lowongan-pkl/mahasiswa');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Semua Data Lowongan PKL',
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
                        'sumber'
                    ]
                ]
            ]);
    }

    public function test_search_lowongan_pkl()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];
        $response = $this->withHeaders($headers)->get('api/lowongan-pkl/mahasiswa/search?q=dev');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Hasil Pencarian',
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
                        'sumber'
                    ]
                ]
            ]);
    }

    public function test_pengajuan_pkl()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];

        $data = [
            'nama_perusahaan' => 'company test',
            'alamat_perusahaan' => 'alamat test',
            'tanggal_mulai' => '2023-02-19 08:42:23',
            'tanggal_selesai' => '2023-02-19 08:42:23'
        ];

        $response = $this->withHeaders($headers)->post('api/pengajuan-pkl/mahasiswa/', $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Pengajuan Berhasil Terkirim',
            ])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id_mahasiswa',
                    'nama_perusahaan',
                    'alamat_perusahaan',
                    'tanggal_mulai',
                    'tanggal_selesai',
                    'status',
                    'created_at',
                    'updated_at',
                    'id_pengajuan'
                ]
            ]);
    }

    public function test_status_pengajuan_pkl()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];
        $response = $this->withHeaders($headers)->get('api/pengajuan-pkl/mahasiswa/status');
        $mahasiswa = Mahasiswa::first();
        
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Status Pengajuan PKL',
                'user' => [
                    'nama' => $mahasiswa->nama,
                    'nama_prodi' => 'Teknik Informatika',
                    'nim' => $mahasiswa->nim
                ],
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'user' => [
                    'nama',
                    'nama_prodi',
                    'nim'
                ],
                'data' => [
                    '*' => [
                        'id_pengajuan',
                        'id_mahasiswa',
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

    public function test_konfirmasi_diterima_pkl()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];

        $data = [
            'id_pengajuan' => 1,
            'id_pembimbing' => 1
        ];

        $response = $this->withHeaders($headers)->post('api/tempat-pkl/mahasiswa', $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Berhasil konfirmasi diterima pkl',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id_pengajuan',
                    'id_pembimbing',
                    'updated_at',
                    'created_at',
                    'id_tempat_pkl'
                ]
            ]);
    }

    public function test_post_biodata_industri()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];

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

        $response = $this->withHeaders($headers)->post('api/biodata-industri/mahasiswa', $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Biodata Industri Berhasil Disimpan',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id_mahasiswa',
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
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];

        $response = $this->withHeaders($headers)->get('api/biodata-industri/mahasiswa/detail');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Detail Biodata Industri',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id_mahasiswa',
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

    public function test_delete_biodata_industri()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];

        $response = $this->withHeaders($headers)->delete('api/biodata-industri/mahasiswa');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Biodata Industri Dihapus',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id_mahasiswa',
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

    public function test_post_jurnal_kegiatan()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];

        $data = [
            'tanggal' => '2023-03-09',
            'minggu' => '1',
            'bidang_pekerjaan' => 'Multi Platform Developer',
            'keterangan' => 'Membuat rancangan websiteMembuat desain Database',
        ];

        $response = $this->withHeaders($headers)->post('api/jurnal-kegiatan/mahasiswa', $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Jurnal Kegiatan Berhasil Ditambahkan',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id_mahasiswa',
                    'id_tempat_pkl',
                    'tanggal',
                    'minggu',
                    'bidang_pekerjaan',
                    'keterangan',
                    'updated_at',
                    'created_at',
                    'id_jurnal_kegiatan'
                ]
            ]);
    }

    public function test_get_jurnal_kegiatan()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];
        $response = $this->withHeaders($headers)->get('api/jurnal-kegiatan/mahasiswa');
        $mahasiswa = Mahasiswa::first();

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Jurnal Kegiatan ' . $mahasiswa->nama,
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    [
                        'minggu',
                        'pdf_url',
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

    public function test_put_jurnal_kegiatan()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];

        $jurnal_kegiatan = JurnalKegiatan::first();

        $newData = [
            'tanggal' => '2023-03-09',
            'minggu' => 1,
            'bidang_pekerjaan' => 'New Field',
            'keterangan' => 'New Description',
        ];

        $response = $this->withHeaders($headers)->put("api/jurnal-kegiatan/mahasiswa/{$jurnal_kegiatan->id_jurnal_kegiatan}", $newData);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Jurnal Kegiatan Berhasil Diperbarui',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id_mahasiswa',
                    'id_tempat_pkl',
                    'tanggal',
                    'minggu',
                    'bidang_pekerjaan',
                    'keterangan',
                    'updated_at',
                    'created_at',
                ]
            ]);
    }

    public function test_delete_jurnal_kegiatan()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];

        $jurnal_kegiatan = JurnalKegiatan::first();

        $response = $this->withHeaders($headers)->delete("api/jurnal-kegiatan/mahasiswa/{$jurnal_kegiatan->id_jurnal_kegiatan}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Jurnal Kegiatan Dihapus',
            ])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id_mahasiswa',
                    'id_tempat_pkl',
                    'tanggal',
                    'minggu',
                    'bidang_pekerjaan',
                    'keterangan',
                    'updated_at',
                    'created_at',
                ]
            ]);
    }

    public function test_get_nilai_pkl()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];

        $response = $this->withHeaders($headers)->get('api/penilaian/mahasiswa');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'pdf_url',
                'data' => [
                    'id_mahasiswa',
                    'nama',
                    'nama_prodi',
                    'nim',
                    'nama_pembimbing',
                    'nik',
                    'id_penilaian_prodi',
                    'id_penilaian_pembimbing',
                    'presentasi',
                    'dokumen',
                    'integritas',
                    'profesionalitas',
                    'bahasa_inggris',
                    'teknologi_informasi',
                    'komunikasi',
                    'kerja_sama',
                    'organisasi',
                    'nilai_akhir',
                    'nilai_huruf',
                ]
            ]);
    }

    public function test_post_daftar_hadir()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];

        $file = UploadedFile::fake()->image('TTD.png');
        
        $data = [
            'hari_tanggal' => '2023-04-10',
            'minggu' => '1',
            'tanda_tangan' => $file,
        ];

        $response = $this->withHeaders($headers)->post('api/daftar-hadir/mahasiswa', $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Daftar Hadir Berhasil Ditambahkan',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id_daftar_hadir',
                    'id_mahasiswa',
                    'hari_tanggal',
                    'minggu',
                    'tanda_tangan',
                ]
            ]);    
    }

    public function test_get_daftar_hadir()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];
        $response = $this->withHeaders($headers)->get('api/daftar-hadir/mahasiswa');
        $mahasiswa = Mahasiswa::first();

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Daftar Hadir ' . $mahasiswa->nama,
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'pdf_url',
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

    public function test_put_daftar_hadir()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];

        $file = UploadedFile::fake()->image('TTD.png');
        $daftar_hadir = DaftarHadir::first();

        $data = [
            'hari_tanggal' => '2023-04-10',
            'minggu' => '1',
            'tanda_tangan' => $file,
        ];

        $response = $this->withHeaders($headers)->put("api/daftar-hadir/mahasiswa/{$daftar_hadir->id_daftar_hadir}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Daftar Hadir Berhasil Diperbarui',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id_daftar_hadir',
                    'id_mahasiswa',
                    'hari_tanggal',
                    'minggu',
                    'tanda_tangan',
                ]
            ]);    
    }

    public function test_delete_daftar_hadir()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];

        $daftar_hadir = DaftarHadir::first();

        $response = $this->withHeaders($headers)->delete("api/daftar-hadir/mahasiswa/{$daftar_hadir->id_daftar_hadir}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Daftar Hadir Terhapus',
            ])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id_daftar_hadir',
                    'id_mahasiswa',
                    'hari_tanggal',
                    'minggu',
                    'tanda_tangan',
                    'created_at',
                    'updated_at'
                ]
            ]);    
    }

    public function test_upload_laporan()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];

        $file = UploadedFile::fake()->create("laporan.pdf", 500);
        
        $data = [
            'file' => $file
        ];

        $response = $this->withHeaders($headers)->post('api/laporan/mahasiswa/upload', $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Laporan berhasil diunggah',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id_laporan',
                    'id_mahasiswa',
                    'file',
                    'tanggal_upload',
                ]
            ]);    
    }

    public function test_get_laporan()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];
        $response = $this->withHeaders($headers)->get('api/laporan/mahasiswa');
        $mahasiswa = Mahasiswa::first();

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Laporan PKL ' . $mahasiswa->nama,
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id_laporan',
                    'nama',
                    'nim',
                    'file',
                    'tanggal_upload',
                ]
            ]);    
    }

    public function test_cancel_send_laporan()
    {
        $headers = ['Authorization' => 'Bearer ' . $this->tokenMahasiswa];

        $laporan = LaporanPKL::first();

        $response = $this->withHeaders($headers)->delete("api/laporan/mahasiswa/{$laporan->id_laporan}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Laporan dibatalkan',
            ])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id_laporan',
                    'id_mahasiswa',
                    'file',
                    'tanggal_upload',
                    'created_at',
                    'updated_at'
                ]
            ]);    
    }
}
