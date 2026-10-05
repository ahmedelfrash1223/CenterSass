<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Branch> */
class BranchFactory extends Factory
{
    protected $model = Branch::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'teacher_id' => TeacherFactory::new(),
            'name' => fake()->company(),
            'address' => fake()->address(),
            'address' => fake()->address(),
            'is_active' => 1,
        ];
    }
}
