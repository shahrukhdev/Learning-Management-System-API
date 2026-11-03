<?php

namespace App\Contracts;

use App\DTOs\Academic\MarkAttendanceDTO;
use Illuminate\Support\Collection;

interface AttendanceInterface
{
    public function getClassAttendance(
        int $academicClassId,
        string $attendanceDate
    ): Collection;

    public function markAttendance(
        MarkAttendanceDTO $dto
    ): Collection;

    public function getAttendanceOverview(
        int $academicClassId,
        string $weekStart
    ): Collection;
}