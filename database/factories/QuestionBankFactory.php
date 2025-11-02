<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuestionBank>
 */
class QuestionBankFactory extends Factory
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
            'name' => fake()->randomElement([
                'Mathematics Question Bank',
                'Science Question Bank',
                'English Question Bank',
                'History Question Bank',
                'Geography Question Bank'
            ]),
            'description' => fake()->sentence(),
        ];
    }
}
