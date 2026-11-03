<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Juz extends Model
{
    protected $table = 'juz';

    protected $fillable = [
        'number',
        'name',
    ];

    public function surahRanges(): HasMany
    {
        return $this->hasMany(JuzSurahRange::class);
    }
}