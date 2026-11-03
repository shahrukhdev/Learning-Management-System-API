<?php

namespace App\Services\Academic;

use App\Contracts\AttendanceInterface;
use App\DTOs\Academic\MarkAttendanceDTO;
use Illuminate\Support\Collection;

class AttendanceService
{
    public function __construct(
        protected AttendanceInterface $repository
    ) {}

    public function getClassAttendance(
        int $academicClassId,
        string $attendanceDate
    ): Collection {
        return $this->repository->getClassAttendance(
            $academicClassId,
            $attendanceDate
        );
    }

    public function markAttendance(
        MarkAttendanceDTO $dto
    ): Collection {
        return $this->repository->markAttendance($dto);
    }

    public function getAttendanceOverview(
        int $academicClassId,
        string $weekStart
    ): Collection {
        return $this->repository->getAttendanceOverview(
            $academicClassId,
            $weekStart
        );
    }
}