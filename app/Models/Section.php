<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'grade_id',
        'name',
    ];

    /**
     * Get the grade that owns the section.
     */
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    /**
     * Get the enrollments for the section.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the monthly exams for the section.
     */
    public function monthlyExams()
    {
        return $this->hasMany(MonthlyExam::class);
    }
}
