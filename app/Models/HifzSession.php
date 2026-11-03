<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HifzSession extends Model
{
    protected $fillable = [
        'student_id',
        'teacher_id',
        'session_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function oldSabaqEntries(): HasMany
    {
        return $this->hasMany(HifzOldSabaqEntry::class);
    }

    public function manzilEntries(): HasMany
    {
        return $this->hasMany(HifzManzilEntry::class);
    }

    public function newSabaqEntries(): HasMany
    {
        return $this->hasMany(HifzNewSabaqEntry::class);
    }
}