<?php

namespace Database\Factories;

use App\Models\StudentProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<StudentProfile> */
class StudentProfileFactory extends Factory
{
    protected $model = StudentProfile::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'student_id' => StudentFactory::new(),
            'guardian_name' => fake()->name(),
            'guardian_phone' => fake()->numerify('012########'),
            'guardian_relation' => 'أب',
            'school_name' => fake()->company(),
            'city' => fake()->city(),
            'address' => fake()->address(),
        ];
    }
}
