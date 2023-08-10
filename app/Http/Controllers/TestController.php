<?php

namespace App\Http\Controllers;

use App\Helpers\SuratHelper;

class TestController extends Controller
{
    public function simulate()
    {
        // Simulasi hari pertama (generateNomorSurat() pertama kali dipanggil)
        $date = now()->format('Y-m-d');
        echo "Tanggal: $date\n";
        echo "Daily Serial Number: " . SuratHelper::generateDailySerialNumber() . "\n";
        echo "Daily Specific: " . SuratHelper::generateDailyNumberForSpecificDay() . "\n";

        // Simulasi hari kedua (setelah pergantian hari)
        $date = now()->addDay()->format('Y-m-d');
        echo "\nTanggal: $date\n";
        echo "Daily Serial Number: " . SuratHelper::generateDailySerialNumber() . "\n";
        echo "Daily Specific: " . SuratHelper::generateDailyNumberForSpecificDay() . "\n";
    }
}
