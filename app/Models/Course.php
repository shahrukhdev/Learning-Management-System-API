<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'duration',
        'is_active',
    ];

    public function academicClasses(): BelongsToMany
    {
        return $this->belongsToMany(AcademicClass::class, 'academic_class_course')->withTimestamps();
    }
}
