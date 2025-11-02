<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttemptAnswer>
 */
class AttemptAnswerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'attempt_id' => 1,
            'question_id' => 1,
            'choice_id' => fake()->numberBetween(1, 10),
            'answer_text' => fake()->sentence(),
            'uploaded_file' => fake()->optional()->imageUrl(),
            'marks_awarded' => fake()->randomFloat(2, 0, 5),
            'auto_graded' => fake()->boolean(70), // 70% chance of being auto-graded
            'graded_by' => fake()->optional()->numberBetween(1, 10),
            'graded_at' => fake()->optional()->dateTimeThisMonth(),
            'saved_at' => fake()->dateTimeThisMonth(),
        ];
    }
}
