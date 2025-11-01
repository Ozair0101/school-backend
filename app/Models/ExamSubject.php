<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSubject extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'monthly_exam_id',
        'subject_id',
        'max_marks',
        'pass_marks',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'max_marks' => 'integer',
            'pass_marks' => 'integer',
        ];
    }

    /**
     * Get the monthly exam that owns the exam subject.
     */
    public function monthlyExam()
    {
        return $this->belongsTo(MonthlyExam::class);
    }

    /**
     * Get the subject that owns the exam subject.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
