<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicClass extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'capacity',
        'is_active',
    ];

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'academic_class_course')->withTimestamps();
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'academic_class_teacher')
            ->withTimestamps();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'academic_class_student'
        )
            ->using(AcademicClassStudent::class)
            ->withTimestamps();
    }
}
