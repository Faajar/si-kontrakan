<?php

namespace App\Support;

class Phone
{
    /**
     * Normalisasi nomor WhatsApp Indonesia ke format 62xxxxxxxxxx (dipakai wa.me).
     * "0812-3456-789" -> "628123456789", "+62 812..." -> "62812...".
     */
    public static function normalize(?string $number): ?string
    {
        if ($number === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $number);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62'.$digits;
        }

        return $digits;
    }

    /** Tautan wa.me dengan draf pesan. */
    public static function waLink(string $number, string $message = ''): string
    {
        $url = 'https://wa.me/'.self::normalize($number);

        return $message === '' ? $url : $url.'?text='.rawurlencode($message);
    }
}
