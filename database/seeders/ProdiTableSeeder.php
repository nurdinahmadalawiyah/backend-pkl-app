<?php

namespace Database\Seeders;

use App\Models\Prodi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProdiTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $prodis = [
            [
                'username' => 'prodiIF',
                'nama_prodi' => 'Teknik Informatika',
                'kode_prodi' => 'IF',
                'nama_ketua_prodi' => 'Castaka Agus Sugianto S, M.Kom., MCS',
                'nidn_ketua_prodi' => '0410048704',
                'password' => bcrypt('prodiiftedc')
            ],
            [
                'username' => 'prodiTK',
                'nama_prodi' => 'Teknik Komputer',
                'kode_prodi' => 'TK',
                'nama_ketua_prodi' => 'Castaka Agus Sugianto S, M.Kom., MCS',
                'nidn_ketua_prodi' => '0410048704',
                'password' => bcrypt('proditktedc')
            ],
            [
                'username' => 'prodiAB',
                'nama_prodi' => 'Alat Berat',
                'kode_prodi' => 'AB',
                'nama_ketua_prodi' => '',
                'nidn_ketua_prodi' => '',
                'password' => bcrypt('prodiabtedc')
            ],
            [
                'username' => 'prodiTM',
                'nama_prodi' => 'Teknik Mesin',
                'kode_prodi' => 'TM',
                'nama_ketua_prodi' => 'Firdani Fauziah, ST.,MT',
                'nidn_ketua_prodi' => '',
                'password' => bcrypt('proditmtedc')
            ],
            [
                'username' => 'prodiMO',
                'nama_prodi' => 'Mesin Otomotif',
                'kode_prodi' => 'MO',
                'nama_ketua_prodi' => 'Didid Dwi Santoso, SST., MT',
                'nidn_ketua_prodi' => '',
                'password' => bcrypt('prodimotedc')
            ],
            [
                'username' => 'prodiAK',
                'nama_prodi' => 'Akuntansi',
                'kode_prodi' => 'AK',
                'nama_ketua_prodi' => 'Apit Yuliman Ermaya, Drs.,M.Ak.Ak.,CA',
                'nidn_ketua_prodi' => '',
                'password' => bcrypt('prodiaktedc')
            ],
            [
                'username' => 'prodiRM',
                'nama_prodi' => 'Rekam Medik dan Informasi Kesehatan',
                'kode_prodi' => 'RM',
                'nama_ketua_prodi' => 'Srimara Soemitra, Dra.,M.M.Kes',
                'nidn_ketua_prodi' => '',
                'password' => bcrypt('prodirmtedc')
            ],
            [
                'username' => 'prodiTE',
                'nama_prodi' => 'Teknik Elektronika',
                'kode_prodi' => 'TE',
                'nama_ketua_prodi' => 'Reni Listiana, MT',
                'nidn_ketua_prodi' => '',
                'password' => bcrypt('proditetedc')
            ],
            [
                'username' => 'prodiKIM',
                'nama_prodi' => 'Teknik Kimia',
                'kode_prodi' => 'KIM',
                'nama_ketua_prodi' => 'Ina Yulianti, M.PKim',
                'nidn_ketua_prodi' => '',
                'password' => bcrypt('prodikimtedc')
            ],
            [
                'username' => 'prodiKB',
                'nama_prodi' => 'Konstruksi Bangunan (Teknik Sipil)',
                'kode_prodi' => 'KB',
                'nama_ketua_prodi' => 'Deddy Misdarpon, MT',
                'nidn_ketua_prodi' => '',
                'password' => bcrypt('prodikbtedc')
            ],
            [
                'username' => 'prodiKA',
                'nama_prodi' => 'Komputerisasi Akutansi',
                'kode_prodi' => 'KA',
                'nama_ketua_prodi' => 'Dedy Suryadi, SE., M.Ak, Ak.,CA',
                'nidn_ketua_prodi' => '',
                'password' => bcrypt('prodikatedc')
            ],
            [
                'username' => 'prodiMID',
                'nama_prodi' => 'Mekanik Industri dan Desain (Teknik Mesin)',
                'kode_prodi' => 'MID',
                'nama_ketua_prodi' => 'Agus Saleh, S.ST.,M.T',
                'nidn_ketua_prodi' => '',
                'password' => bcrypt('prodimidtedc')
            ],
            [
                'username' => 'prodiTOI',
                'nama_prodi' => 'Teknik Otomasi Industri',
                'kode_prodi' => 'TOI',
                'nama_ketua_prodi' => 'Usman E, Dipl.Ed,S.ST.,M.Pd',
                'nidn_ketua_prodi' => '',
                'password' => bcrypt('proditoitedc')
            ],
        ];

        foreach ($prodis as $prodi) {
            Prodi::create($prodi);
        }
    }
}
