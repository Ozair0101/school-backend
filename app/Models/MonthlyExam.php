<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyExam extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_id',
        'grade_id',
        'section_id',
        'month',
        'year',
        'exam_date',
        'description',
        'online_enabled',
        'start_time',
        'end_time',
        'duration_minutes',
        'allow_multiple_attempts',
        'max_attempts',
        'shuffle_questions',
        'shuffle_choices',
        'negative_marking',
        'passing_percentage',
        'access_code',
        'random_pool',
        'show_answers_after',
        'auto_publish_results',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'online_enabled' => 'boolean',
            'allow_multiple_attempts' => 'boolean',
            'shuffle_questions' => 'boolean',
            'shuffle_choices' => 'boolean',
            'random_pool' => 'boolean',
            'show_answers_after' => 'boolean',
            'auto_publish_results' => 'boolean',
            'negative_marking' => 'decimal:2',
            'passing_percentage' => 'decimal:2',
            'exam_date' => 'date',
        ];
    }

    /**
     * Get the school that owns the monthly exam.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the grade that owns the monthly exam.
     */
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    /**
     * Get the section that owns the monthly exam.
     */
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the exam subjects for the monthly exam.
     */
    public function examSubjects()
    {
        return $this->hasMany(ExamSubject::class);
    }

    /**
     * Get the exam questions for the monthly exam.
     */
    public function examQuestions()
    {
        return $this->hasMany(ExamQuestion::class);
    }

    /**
     * Get the student attempts for the monthly exam.
     */
    public function studentAttempts()
    {
        return $this->hasMany(StudentAttempt::class);
    }

    /**
     * Get the proctoring events for the monthly exam.
     */
    public function proctoringEvents()
    {
        return $this->hasManyThrough(ProctoringEvent::class, StudentAttempt::class);
    }

    /**
     * Get the exam aggregates for the monthly exam.
     */
    public function examAggregates()
    {
        return $this->hasMany(ExamAggregate::class);
    }
}
