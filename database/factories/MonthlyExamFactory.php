<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MonthlyExam>
 */
class MonthlyExamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => 1,
            'grade_id' => 1,
            'section_id' => 1,
            'month' => fake()->numberBetween(1, 12),
            'year' => fake()->year(),
            'exam_date' => fake()->date(),
            'description' => fake()->sentence(),
            'online_enabled' => true,
            'duration_minutes' => fake()->randomElement([30, 45, 60, 90, 120]),
            'passing_percentage' => fake()->numberBetween(50, 80),
        ];
    }
}
