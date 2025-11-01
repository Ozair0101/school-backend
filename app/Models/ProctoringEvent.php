<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProctoringEvent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'attempt_id',
        'event_type',
        'event_time',
        'details',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_time' => 'datetime',
            'details' => 'array',
        ];
    }

    /**
     * Get the student attempt that owns the proctoring event.
     */
    public function attempt()
    {
        return $this->belongsTo(StudentAttempt::class);
    }
}
