<?php

namespace Database\Seeders;

use App\Enums\Teacher\TeacherRoleEnum;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        // =============================================
        // Owner
        // =============================================
        $owner = Teacher::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'TCH-' . strtoupper(Str::random(8)),
            'username' => 'admin_teacher',
            'name' => 'Demo Admin Teacher',
            'email' => 'owner@example.com',
            'password' => Hash::make('P@ssw0rd'),
            'phone' => '0123456789',
            'role' => TeacherRoleEnum::OWNER->value,
            'is_active' => 1,
        ]);

    }
}
