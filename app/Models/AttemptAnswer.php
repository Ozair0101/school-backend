<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttemptAnswer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'attempt_id',
        'question_id',
        'choice_id',
        'answer_text',
        'uploaded_file',
        'marks_awarded',
        'auto_graded',
        'graded_by',
        'graded_at',
        'saved_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'marks_awarded' => 'decimal:2',
            'auto_graded' => 'boolean',
            'graded_at' => 'datetime',
            'saved_at' => 'datetime',
        ];
    }

    /**
     * Get the student attempt that owns the attempt answer.
     */
    public function attempt()
    {
        return $this->belongsTo(StudentAttempt::class, 'attempt_id');
    }

    /**
     * Get the question that owns the attempt answer.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the choice that owns the attempt answer.
     */
    public function choice()
    {
        return $this->belongsTo(Choice::class);
    }

    /**
     * Get the teacher who graded the attempt answer.
     */
    public function gradedBy()
    {
        return $this->belongsTo(Teacher::class, 'graded_by');
    }
}
