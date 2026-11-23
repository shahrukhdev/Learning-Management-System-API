<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HifzSetting extends Model
{
    protected $fillable = [
        'course_id',
        'total_pages',
        'total_ayats',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}