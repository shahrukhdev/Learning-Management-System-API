<?php

namespace App\Contracts\Hifz;

use App\Models\Surah;
use Illuminate\Database\Eloquent\Collection;

interface SurahInterface
{
    public function getAllSurahs(): Collection;

    public function getSurah(int $id): Surah;
}