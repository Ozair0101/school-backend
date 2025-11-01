<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bank_id',
        'author_id',
        'type',
        'prompt',
        'default_marks',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => 'string',
            'default_marks' => 'decimal:2',
            'metadata' => 'array',
        ];
    }

    /**
     * Get the question bank that owns the question.
     */
    public function bank()
    {
        return $this->belongsTo(QuestionBank::class);
    }

    /**
     * Get the teacher who authored the question.
     */
    public function author()
    {
        return $this->belongsTo(Teacher::class, 'author_id');
    }

    /**
     * Get the choices for the question.
     */
    public function choices()
    {
        return $this->hasMany(Choice::class);
    }

    /**
     * Get the exam questions for the question.
     */
    public function examQuestions()
    {
        return $this->hasMany(ExamQuestion::class);
    }

    /**
     * Get the attempt answers for the question.
     */
    public function attemptAnswers()
    {
        return $this->hasMany(AttemptAnswer::class);
    }
}
