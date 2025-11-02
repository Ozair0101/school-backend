<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enrollment>
 */
class EnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => 1,
            'grade_id' => 1,
            'section_id' => 1,
            'academic_year' => '2025-2026',
            'roll_no' => fake()->unique()->numberBetween(1, 100),
        ];
    }
}
