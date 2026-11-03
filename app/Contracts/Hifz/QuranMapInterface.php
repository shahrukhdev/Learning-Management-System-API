<?php

namespace App\Contracts\Hifz;

use Illuminate\Database\Eloquent\Collection;

interface QuranMapInterface
{
    public function getStudentQuranMap(int $studentId): Collection;
}