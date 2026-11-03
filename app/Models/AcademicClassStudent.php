
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AcademicClassStudent extends Pivot
{
    protected $table = 'academic_class_student';

    public $incrementing = true;

    protected $fillable = [
        'student_id',
        'academic_class_id',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class);
    }
}
