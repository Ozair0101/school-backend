<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttempt extends Model
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
        'started_at',
        'finished_at',
        'duration_seconds',
        'status',
        'total_score',
        'percent',
        'ip_address',
        'device_info',
        'attempt_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'duration_seconds' => 'integer',
            'total_score' => 'decimal:2',
            'percent' => 'decimal:2',
            'status' => 'string',
        ];
    }

    /**
     * Get the monthly exam that owns the student attempt.
     */
    public function monthlyExam()
    {
        return $this->belongsTo(MonthlyExam::class);
    }

    /**
     * Get the student that owns the student attempt.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the attempt answers for the student attempt.
     */
    public function attemptAnswers()
    {
        return $this->hasMany(AttemptAnswer::class);
    }

    /**
     * Get the proctoring events for the student attempt.
     */
    public function proctoringEvents()
    {
        return $this->hasMany(ProctoringEvent::class);
    }
}
