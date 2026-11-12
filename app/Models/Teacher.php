<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function academicClasses(): BelongsToMany
    {
        return $this->belongsToMany(AcademicClass::class, 'academic_class_teacher')
            ->withTimestamps();
    }

    public function hifzSessions(): HasMany
    {
        return $this->hasMany(HifzSession::class, 'recorded_by', 'user_id');
    }
}
