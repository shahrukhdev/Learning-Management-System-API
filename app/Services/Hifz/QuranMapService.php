<?php

namespace App\Services\Hifz;

use App\Contracts\Hifz\QuranMapInterface;
use Illuminate\Database\Eloquent\Collection;

class QuranMapService
{
    public function __construct(
        protected QuranMapInterface $repository
    ) {}

    public function getStudentQuranMap(int $studentId): Collection
    {
        return $this->repository->getStudentQuranMap($studentId);
    }
}