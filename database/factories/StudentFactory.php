<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
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
            'admission_no' => Str::random(10),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'dob' => fake()->date(),
            'gender' => fake()->randomElement(['male', 'female']),
            'contact' => json_encode([
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->phoneNumber(),
            ]),
        ];
    }
}
