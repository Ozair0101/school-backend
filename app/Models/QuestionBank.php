<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
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
        'created_by',
    ];

    /**
     * Get the school that owns the question bank.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the teacher who created the question bank.
     */
    public function creator()
    {
        return $this->belongsTo(Teacher::class, 'created_by');
    }

    /**
     * Get the questions in the question bank.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
