<?php

namespace App\Services\Academic;

use App\Contracts\AttendanceInterface;
use App\Contracts\StudentInterface;
use App\DTOs\Academic\MarkAttendanceDTO;
use App\Enums\AttendancePeriod;
use App\Enums\AttendanceStatus;
use App\Exceptions\ApiException;
use App\Models\User;
use Illuminate\Support\Collection;

class AttendanceService
{
    private const STUDENT_ROLE = 'Student';

    public function __construct(
        protected AttendanceInterface $repository,
        protected StudentInterface $studentRepository,
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

    /**
     * The signed-in student's own attendance for the period: summary counts
     * and one record per marked day (newest first). Always returns data;
     * a student without records gets zero counts and an empty list.
     *
     * @return array{period: string, start_date: string, end_date: string, summary: array, records: Collection}
     */
    public function getMyAttendance(User $user, AttendancePeriod $period): array
    {
        if (!$user->hasRole(self::STUDENT_ROLE)) {
            throw new ApiException(
                message: 'Only students can view their own attendance.',
                status: 403
            );
        }

        $endDate = now()->startOfDay();
        $startDate = $period->startDate($endDate);

        $student = $this->studentRepository->findByUserId($user->id);

        $records = $student
            ? $this->repository->getStudentAttendanceRecords($student->id, $startDate, $endDate)
            : collect();

        return [
            'period' => $period->value,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'summary' => $this->summary($records),
            'records' => $records,
        ];
    }

    /**
     * present + late count as attended (same formula as the dashboards).
     */
    private function summary(Collection $records): array
    {
        $counts = $records->countBy(fn ($record) => $record->status->value);

        $present = (int) $counts->get(AttendanceStatus::PRESENT->value, 0);
        $late = (int) $counts->get(AttendanceStatus::LATE->value, 0);
        $absent = (int) $counts->get(AttendanceStatus::ABSENT->value, 0);
        $total = $present + $late + $absent;

        return [
            'total_classes' => $total,
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'attendance_rate' => $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0,
        ];
    }
}
