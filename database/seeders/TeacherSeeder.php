<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        Teacher::create([
            'uuid' => (string)Str::uuid(),
            'code' => 'TCH-' . strtoupper(Str::random(8)),
            'username' => 'teacher',
            'name' => 'Demo Teacher',
            'email' => 'teacher@example.com',
            'password' => Hash::make('P@ssw0rd'),
            'is_active' => 1,
        ]);
    }
}
