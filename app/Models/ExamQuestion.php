<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'monthly_exam_id',
        'question_id',
        'marks',
        'sequence',
        'pool_tag',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'marks' => 'decimal:2',
            'sequence' => 'integer',
        ];
    }

    /**
     * Get the monthly exam that owns the exam question.
     */
    public function monthlyExam()
    {
        return $this->belongsTo(MonthlyExam::class);
    }

    /**
     * Get the question that owns the exam question.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
