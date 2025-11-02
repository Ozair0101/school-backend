<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExamQuestion>
 */
class ExamQuestionFactory extends Factory
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
            'question_id' => 1,
            'marks' => fake()->randomElement([1, 2, 3, 4, 5]),
            'sequence' => fake()->numberBetween(1, 50),
        ];
    }
}
