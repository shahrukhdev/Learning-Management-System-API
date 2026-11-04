<?php

namespace App\Contracts\Hifz;

interface HifzStatisticsInterface
{
    public function getStudentHifzStatistics(int $studentId): array;
}
