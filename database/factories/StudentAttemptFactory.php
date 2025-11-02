<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentAttempt>
 */
class StudentAttemptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'monthly_exam_id' => 1,
            'student_id' => 1,
            'started_at' => fake()->dateTimeThisMonth(),
            'finished_at' => fake()->dateTimeThisMonth(),
            'duration_seconds' => fake()->numberBetween(600, 7200),
            'status' => fake()->randomElement(['in_progress', 'submitted', 'grading', 'graded', 'abandoned']),
            'total_score' => fake()->randomFloat(2, 0, 100),
            'percent' => fake()->randomFloat(2, 0, 100),
            'ip_address' => fake()->ipv4(),
            'device_info' => fake()->userAgent(),
            'attempt_token' => Str::random(32),
        ];
    }
}
