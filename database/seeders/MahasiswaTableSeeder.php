<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MahasiswaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mahasiswas = [
            [
                'username' => 'nurdin',
                'nama' => 'Nurdin A. Alawiyah',
                'nim' => 'D119111068',
                'prodi' => 1,
                'semester' => '8 (Delapan)',
                'email' => 'nurdinahamada@gmail.com',
                'nomor_hp' => '08977612607',
                'password' => bcrypt('123456789')
            ],
            [
                'username' => 'revaldi',
                'nama' => 'Revaldi Dwi Octavian',
                'nim' => 'D119111076',
                'prodi' => 2,
                'semester' => '8 (Delapan)',
                'email' => 'revdo@gmail.com',
                'nomor_hp' => '088639126738',
                'password' => bcrypt('123456789')
            ],
        ];

        foreach ($mahasiswas as $mahasiswa) {
            Mahasiswa::create($mahasiswa);
        }
    }
}
