<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'uuid' => (string)Str::uuid(),
            'code' => 'ADM-' . strtoupper(Str::random(8)),
            'username' => 'admin',
            'name' => 'System Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('P@ssw0rd'),
            'is_active' => 1,
        ]);
    }
}
