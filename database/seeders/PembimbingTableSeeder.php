<?php

namespace Database\Seeders;

use App\Models\Pembimbing;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PembimbingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pembimbings = [
            [
                'username' => 'alian',
                'nama' => 'Alian Hakim',
                'nik' => '87127572',
                'password' => bcrypt('123456789')
            ],
            [
                'username' => 'ilham',
                'nama' => 'Ilham Ramdan Pratama',
                'nik' => '324523',
                'password' => bcrypt('123456789')
            ],
        ];

        foreach ($pembimbings as $pembimbing) {
            Pembimbing::create($pembimbing);
        }
    }
}
