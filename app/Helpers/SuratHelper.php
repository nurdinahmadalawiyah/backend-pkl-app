<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class SuratHelper
{
    public static function generateDailySerialNumber()
    {
        $cacheKey = 'daily_serial_number_' . date('Ymd');
        $lastGeneratedDate = Cache::get($cacheKey . '_last_generated');

        if ($lastGeneratedDate === null) {
            Cache::put($cacheKey, 1, now()->endOfDay());
        } else {
            Cache::increment($cacheKey);
        }

        $dailySerialNumber = Cache::get($cacheKey);
        Cache::put($cacheKey . '_last_generated', now(), now()->endOfDay());

        return $dailySerialNumber;
    }

    public static function generateDailyNumberForSpecificDay()
    {
        $cacheKey = 'daily_number_for_specific_day_' . date('Ymd');
        $lastGeneratedDate = Cache::get($cacheKey . '_last_generated');

        if ($lastGeneratedDate === null || $lastGeneratedDate !== date('Ymd')) {
            Cache::put($cacheKey, 1, now()->endOfDay());
        } else {
            Cache::increment($cacheKey);
        }

        $dailyNumberForSpecificDay = Cache::get($cacheKey);
        Cache::put($cacheKey . '_last_generated', now(), now()->endOfDay());

        return str_pad($dailyNumberForSpecificDay, 3, '0', STR_PAD_LEFT);
    }

    public static function generateNomorSurat()
    {
        $romawi = self::romanNumerals(date('m'));

        return self::generateDailyNumberForSpecificDay() . "." .  self::generateDailySerialNumber() . "/PKL/TEDC-BAA/{$romawi}/" . date('Y');
    }

    public static function romanNumerals($num)
    {
        $n = intval($num);
        $result = '';
        // Define a lookup array that contains all of the Roman numerals.
        $lookup = [
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1,
        ];
        foreach ($lookup as $roman => $value) {
            // Determine the number of matches.
            $matches = intval($n / $value);
            // Add the same number of characters to the result.
            $result .= str_repeat($roman, $matches);
            // Subtract the current value from the number.
            $n = $n % $value;
        }
        // The Roman numeral should be built.
        return $result;
    }
}
