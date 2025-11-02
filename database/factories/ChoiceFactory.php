<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Choice>
 */
class ChoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question_id' => 1,
            'choice_text' => fake()->sentence(),
            'is_correct' => fake()->boolean(30), // 30% chance of being correct
            'position' => fake()->numberBetween(1, 4),
        ];
    }
}
