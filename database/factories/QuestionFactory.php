<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bank_id' => 1,
            'author_id' => 1,
            'type' => fake()->randomElement(['mcq', 'tf', 'numeric', 'short', 'essay', 'file']),
            'prompt' => fake()->sentence(),
            'default_marks' => fake()->randomElement([1, 2, 3, 4, 5]),
        ];
    }
}
