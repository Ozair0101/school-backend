<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAggregate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'monthly_exam_id',
        'student_id',
        'total_marks',
        'percent',
        'rank',
        'published',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_marks' => 'decimal:2',
            'percent' => 'decimal:2',
            'rank' => 'integer',
            'published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Get the monthly exam that owns the exam aggregate.
     */
    public function monthlyExam()
    {
        return $this->belongsTo(MonthlyExam::class);
    }

    /**
     * Get the student that owns the exam aggregate.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
