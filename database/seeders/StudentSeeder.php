<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = Teacher::first();

        Student::create([
            'teacher_id' => $teacher->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'STD-'.strtoupper(Str::random(8)),
            'name' => 'Demo Student',
            'is_active' => 1,
        ]);
    }
}
