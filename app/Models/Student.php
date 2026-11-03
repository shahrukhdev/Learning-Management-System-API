
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admission_number',
        'date_of_birth',
        'gender',
        'phone',
        'address',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
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

    public function surahProgress(): HasMany
    {
        return $this->hasMany(StudentSurahProgress::class);
    }
}
