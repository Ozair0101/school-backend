<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_id',
        'admission_no',
        'first_name',
        'last_name',
        'dob',
        'gender',
        'contact',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'contact' => 'array',
            'dob' => 'date',
        ];
    }

    /**
     * Get the school that owns the student.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the enrollments for the student.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the student attempts for the student.
     */
    public function attempts()
    {
        return $this->hasMany(StudentAttempt::class);
    }

    /**
     * Get the exam aggregates for the student.
     */
    public function examAggregates()
    {
        return $this->hasMany(ExamAggregate::class);
    }
}
