<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add the column
        Schema::table('students', function (Blueprint $table) {
            $table->string('barcode_digits', 12)->nullable()->after('code');
        });

        // 2. Populate existing students
        DB::table('students')->orderBy('id')->get()->each(function ($student) {
            $uuid = $student->uuid ?? (string) Str::uuid();
            $digits = self::deriveDigits($uuid);

            // Ensure uniqueness on the fly
            $attempt = 0;
            while (DB::table('students')->where('barcode_digits', $digits)->exists() && $attempt < 10) {
                $digits = str_pad((string) random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);
                $attempt++;
            }

            DB::table('students')->where('id', $student->id)->update([
                'uuid' => $uuid,
                'barcode_digits' => $digits,
            ]);
        });

        // 3. Add unique index after population
        Schema::table('students', function (Blueprint $table) {
            $table->unique('barcode_digits');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['barcode_digits']);
            $table->dropColumn('barcode_digits');
        });
    }

    /**
     * Convert uuid → 12-digit deterministic string.
     */
    private static function deriveDigits(string $uuid): string
    {
        $hex = str_replace('-', '', $uuid);

        $digits = '';
        foreach (str_split($hex, 4) as $chunk) {
            $digits .= str_pad((string) hexdec($chunk), 5, '0', STR_PAD_LEFT);
        }

        return substr($digits, 0, 12);
    }
};
