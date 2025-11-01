<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'question_id',
        'choice_text',
        'is_correct',
        'position',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
        ];
    }

    /**
     * Get the question that owns the choice.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the attempt answers for the choice.
     */
    public function attemptAnswers()
    {
        return $this->hasMany(AttemptAnswer::class);
    }
}
