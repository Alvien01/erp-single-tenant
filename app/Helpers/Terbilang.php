<?php

namespace App\Helpers;

class Terbilang
{
    private static $words = [
        '', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima',
        'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'
    ];

    public static function convert($number): string
    {
        $number = abs((int) $number);

        if ($number < 12) {
            return self::$words[$number];
        } elseif ($number < 20) {
            return self::$words[$number - 10] . ' Belas';
        } elseif ($number < 100) {
            return self::$words[(int)($number / 10)] . ' Puluh ' . self::$words[$number % 10];
        } elseif ($number < 200) {
            return 'Seratus ' . self::convert($number - 100);
        } elseif ($number < 1000) {
            return self::$words[(int)($number / 100)] . ' Ratus ' . self::convert($number % 100);
        } elseif ($number < 2000) {
            return 'Seribu ' . self::convert($number - 1000);
        } elseif ($number < 1000000) {
            return self::convert((int)($number / 1000)) . ' Ribu ' . self::convert($number % 1000);
        } elseif ($number < 1000000000) {
            return self::convert((int)($number / 1000000)) . ' Juta ' . self::convert($number % 1000000);
        } elseif ($number < 1000000000000) {
            return self::convert((int)($number / 1000000000)) . ' Miliar ' . self::convert($number % 1000000000);
        } elseif ($number < 1000000000000000) {
            return self::convert((int)($number / 1000000000000)) . ' Triliun ' . self::convert($number % 1000000000000);
        }

        return '';
    }

    public static function rupiah($number): string
    {
        return trim(self::convert($number)) . ' Rupiah';
    }
}
