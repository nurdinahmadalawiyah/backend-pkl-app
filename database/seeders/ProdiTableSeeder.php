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
        ];

        foreach ($prodis as $prodi) {
            Prodi::create($prodi);
        }
    }
}
