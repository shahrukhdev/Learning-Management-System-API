
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AcademicClassCourse extends Pivot
{
    protected $table = 'academic_class_course';

    public $incrementing = true;

    protected $fillable = [
        'academic_class_id',
        'course_id',
    ];

    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
