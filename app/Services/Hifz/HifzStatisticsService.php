<?php

namespace App\Services\Hifz;

use App\Contracts\Hifz\HifzStatisticsInterface;

class HifzStatisticsService
{
    public function __construct(
        protected HifzStatisticsInterface $repository
    ) {}

    public function getStudentHifzStatistics(int $studentId): array
    {
        return $this->repository->getStudentHifzStatistics($studentId);
    }
}
