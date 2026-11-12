<?php

namespace App\Models;

use App\Enums\ProgressGrade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProgress extends Model
{
    protected $table = 'student_progress';

    protected $fillable = [
        'student_id',
        'course_id',
        'recorded_by',
        'lesson',
        'grade',
        'notes',
        'progress_date',
    ];

    protected function casts(): array
    {
        return [
            'grade' => ProgressGrade::class,
            'progress_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
