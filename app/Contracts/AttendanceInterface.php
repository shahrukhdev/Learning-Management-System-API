<?php

namespace App\Contracts;

use App\DTOs\Academic\MarkAttendanceDTO;
use Carbon\Carbon;
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

    /**
     * One attendance record per day for the student between the given dates,
     * newest first. If the student was marked in several classes on the same
     * day, the most recently marked record wins.
     */
    public function getStudentAttendanceRecords(
        int $studentId,
        Carbon $startDate,
        Carbon $endDate
    ): Collection;
}