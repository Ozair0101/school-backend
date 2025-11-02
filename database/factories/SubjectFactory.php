<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
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
                'Mathematics', 'Science', 'English', 'History',
                'Geography', 'Physics', 'Chemistry', 'Biology',
                'Computer Science', 'Art', 'Music', 'Physical Education'
            ]),
            'code' => fake()->lexify('???###'),
            'default_max_marks' => fake()->randomElement([50, 100, 150]),
            'pass_marks' => fake()->randomElement([25, 40, 50, 60, 75]),
        ];
    }
}
