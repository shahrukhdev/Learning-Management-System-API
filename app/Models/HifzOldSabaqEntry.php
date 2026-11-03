<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HifzOldSabaqEntry extends Model
{
    protected $fillable = [
        'hifz_session_id',
        'from_surah_id',
        'to_surah_id',
        'status',
        'strength',
        'mistakes',
    ];

    public function hifzSession(): BelongsTo
    {
        return $this->belongsTo(HifzSession::class);
    }

    public function fromSurah(): BelongsTo
    {
        return $this->belongsTo(Surah::class, 'from_surah_id');
    }

    public function toSurah(): BelongsTo
    {
        return $this->belongsTo(Surah::class, 'to_surah_id');
    }
}