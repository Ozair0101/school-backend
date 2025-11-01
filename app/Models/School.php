<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address',
    ];

    /**
     * Get the grades for the school.
     */
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Get the teachers for the school.
     */
    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    /**
     * Get the students for the school.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get the subjects for the school.
     */
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    /**
     * Get the question banks for the school.
     */
    public function questionBanks()
    {
        return $this->hasMany(QuestionBank::class);
    }

    /**
     * Get the monthly exams for the school.
     */
    public function monthlyExams()
    {
        return $this->hasMany(MonthlyExam::class);
    }
}
