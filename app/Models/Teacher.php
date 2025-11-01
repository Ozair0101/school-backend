<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_id',
        'full_name',
        'email',
        'phone',
    ];

    /**
     * Get the school that owns the teacher.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the question banks created by the teacher.
     */
    public function questionBanks()
    {
        return $this->hasMany(QuestionBank::class, 'created_by');
    }

    /**
     * Get the questions authored by the teacher.
     */
    public function questions()
    {
        return $this->hasMany(Question::class, 'author_id');
    }

    /**
     * Get the teacher subjects for the teacher.
     */
    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class);
    }

    /**
     * Get the attempt answers graded by the teacher.
     */
    public function gradedAnswers()
    {
        return $this->hasMany(AttemptAnswer::class, 'graded_by');
    }
}
