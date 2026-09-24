<?php

namespace App\Traits\Student;

use App\Models\Student;
use Illuminate\Support\Str;

trait GeneratesBarcode
{
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Student $student) {
            $student->uuid ??= (string) Str::uuid();

            if (empty($student->barcode_digits)) {
                $student->barcode_digits = self::generateUniqueBarcodeDigits($student->uuid);
            }

            if (empty($student->code)) {
                $student->code = 'STD-'.$student->barcode_digits;
            }
        });
    }

    public static function deriveBarcodeDigits(string $uuid): string
    {
        $hex = str_replace('-', '', $uuid);
        $digits = '';
        foreach (str_split($hex, 4) as $chunk) {
            $digits .= str_pad((string) hexdec($chunk), 5, '0', STR_PAD_LEFT);
        }

        return substr($digits, 0, 12);
    }

    public static function generateUniqueBarcodeDigits(string $uuid): string
    {
        $digits = self::deriveBarcodeDigits($uuid);
        $attempts = 0;
        while (self::where('barcode_digits', $digits)->exists()) {
            $digits = str_pad((string) random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);
            $attempts++;

            if ($attempts >= 10) {
                $digits = str_pad((string) random_int(0, 99999999999999), 14, '0', STR_PAD_LEFT);
                break;
            }
        }

        return $digits;
    }
}
