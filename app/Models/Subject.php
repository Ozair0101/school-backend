<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_id',
        'name',
        'code',
        'default_max_marks',
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
            'default_max_marks' => 'decimal:2',
            'pass_marks' => 'decimal:2',
        ];
    }

    /**
     * Get the school that owns the subject.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the teacher subjects for the subject.
     */
    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class);
    }

    /**
     * Get the exam subjects for the subject.
     */
    public function examSubjects()
    {
        return $this->hasMany(ExamSubject::class);
    }

    /**
     * Get the exam questions for the subject.
     */
    public function examQuestions()
    {
        return $this->hasManyThrough(ExamQuestion::class, ExamSubject::class);
    }
}
