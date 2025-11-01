<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
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
        'level',
    ];

    /**
     * Get the school that owns the grade.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the sections for the grade.
     */
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    /**
     * Get the enrollments for the grade.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the teacher subjects for the grade.
     */
    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class);
    }

    /**
     * Get the monthly exams for the grade.
     */
    public function monthlyExams()
    {
        return $this->hasMany(MonthlyExam::class);
    }
}
