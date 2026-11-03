<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Surah extends Model
{
    protected $fillable = [
        'number',
        'name',
        'arabic',
        'ayahs',
    ];

    public function juzSurahRanges(): HasMany
    {
        return $this->hasMany(JuzSurahRange::class);
    }

    public function studentProgress(): HasMany
    {
        return $this->hasMany(StudentSurahProgress::class);
    }

    public function oldSabaqFromEntries(): HasMany
    {
        return $this->hasMany(HifzOldSabaqEntry::class, 'from_surah_id');
    }

    public function oldSabaqToEntries(): HasMany
    {
        return $this->hasMany(HifzOldSabaqEntry::class, 'to_surah_id');
    }

    public function manzilFromEntries(): HasMany
    {
        return $this->hasMany(HifzManzilEntry::class, 'from_surah_id');
    }

    public function manzilToEntries(): HasMany
    {
        return $this->hasMany(HifzManzilEntry::class, 'to_surah_id');
    }

    public function newSabaqFromEntries(): HasMany
    {
        return $this->hasMany(HifzNewSabaqEntry::class, 'from_surah_id');
    }

    public function newSabaqToEntries(): HasMany
    {
        return $this->hasMany(HifzNewSabaqEntry::class, 'to_surah_id');
    }
}