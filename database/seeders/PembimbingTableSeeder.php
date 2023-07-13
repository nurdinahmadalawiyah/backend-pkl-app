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
                'username' => 'agussetiawan',
                'nama' => 'Agus Setiawan',
                'nik' => '32452233',
                'password' => bcrypt('123456789')
            ],
            [
                'username' => 'ahmadyusuf',
                'nama' => 'Ahmad Yusuf',
                'nik' => '98765432',
                'password' => bcrypt('123456789')
            ],
            [
                'username' => 'budisantoso',
                'nama' => 'Budi Santoso',
                'nik' => '76543210',
                'password' => bcrypt('123456789')
            ],
                        
        ];

        foreach ($pembimbings as $pembimbing) {
            Pembimbing::create($pembimbing);
        }
    }
}
