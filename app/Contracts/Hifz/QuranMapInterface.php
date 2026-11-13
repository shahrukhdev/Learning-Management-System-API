<?php

namespace App\Contracts\Hifz;

use Illuminate\Database\Eloquent\Collection;

interface QuranMapInterface
{
    public function getStudentQuranMap(int $studentId): Collection;

    /**
     * All surahs with an empty studentProgress relation (every surah not-started).
     */
    public function getEmptyQuranMap(): Collection;
}