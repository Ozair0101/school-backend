<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProctoringEvent>
 */
class ProctoringEventFactory extends Factory
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
            'event_type' => fake()->randomElement(['tab_hidden', 'tab_visible', 'snapshot_captured', 'suspicious_activity']),
            'event_time' => fake()->dateTimeThisMonth(),
            'details' => json_encode([
                'description' => fake()->sentence(),
                'severity' => fake()->randomElement(['low', 'medium', 'high']),
            ]),
        ];
    }
}
