<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExamSubject>
 */
class ExamSubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $maxMarks = fake()->randomElement([50, 100, 150]);
        return [
            'monthly_exam_id' => 1,
            'subject_id' => 1,
            'max_marks' => $maxMarks,
            'pass_marks' => (int) ($maxMarks * 0.4), // 40% pass marks
        ];
    }
}
