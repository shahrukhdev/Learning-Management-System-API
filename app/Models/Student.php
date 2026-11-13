<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'admission_number',
        'year',
        'date_of_birth',
        'gender',
        'phone',
        'address',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'year' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function academicClasses(): BelongsToMany
    {
        return $this->belongsToMany(
            AcademicClass::class,
            'academic_class_student'
        )
            ->using(AcademicClassStudent::class)
            ->withTimestamps();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function hifzSessions(): HasMany
    {
        return $this->hasMany(HifzSession::class);
    }

    public function progressRecords(): HasMany
    {
        return $this->hasMany(StudentProgress::class);
    }

    public function surahProgress(): HasMany
    {
        return $this->hasMany(StudentSurahProgress::class);
    }
}
