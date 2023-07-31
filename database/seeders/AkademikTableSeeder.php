<?php

namespace Database\Seeders;

use App\Models\Akademik;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AkademikTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $akademiks = [
            [
                'username' => 'akademik',
                'password' => bcrypt('akademiktedc')
            ],
        ];

        foreach ($akademiks as $akademik) {
            Akademik::create($akademik);
        }
    }
}
