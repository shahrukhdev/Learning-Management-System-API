<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JuzSurahRange extends Model
{
    protected $fillable = [
        'juz_id',
        'surah_id',
        'start_ayah',
        'end_ayah',
    ];

    public function juz(): BelongsTo
    {
        return $this->belongsTo(Juz::class);
    }

    public function surah(): BelongsTo
    {
        return $this->belongsTo(Surah::class);
    }
}