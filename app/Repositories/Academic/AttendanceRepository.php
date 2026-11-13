<?php

namespace App\Repositories\Academic;

use App\Contracts\AttendanceInterface;
use App\DTOs\Academic\MarkAttendanceDTO;
use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceRepository implements AttendanceInterface
{
    public function getClassAttendance(
        int $academicClassId,
        string $attendanceDate
    ): Collection {
        return Student::query()
            ->with([
                'user',
                'attendances' => function ($query) use ($academicClassId, $attendanceDate) {
                    $query->where('academic_class_id', $academicClassId)
                        ->whereDate('attendance_date', $attendanceDate);
                },
            ])
            ->whereHas('academicClasses', function ($query) use ($academicClassId) {
                $query->where('academic_classes.id', $academicClassId);
            })
            ->orderBy('admission_number')
            ->get();
    }

    public function markAttendance(
        MarkAttendanceDTO $dto
    ): Collection {
        return DB::transaction(function () use ($dto) {
            foreach ($dto->attendances as $attendance) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $attendance['student_id'],
                        'academic_class_id' => $dto->academicClassId,
                        'attendance_date' => $dto->attendanceDate,
                    ],
                    [
                        'status' => $attendance['status'],
                        'marked_by' => Auth::id(),
                    ]
                );
            }

            return $this->getClassAttendance(
                $dto->academicClassId,
                $dto->attendanceDate
            );
        });
    }

    public function getAttendanceOverview(
        int $academicClassId,
        string $weekStart
    ): Collection {
        $startDate = CarbonImmutable::parse($weekStart)
            ->startOfWeek(Carbon::MONDAY);

        $endDate = $startDate->addDays(4);

        return Student::query()
            ->with([
                'user',
                'attendances' => function ($query) use ($academicClassId, $startDate, $endDate) {
                    $query->where('academic_class_id', $academicClassId)
                        ->whereBetween('attendance_date', [
                            $startDate->toDateString(),
                            $endDate->toDateString(),
                        ]);
                },
            ])
            ->whereHas('academicClasses', function ($query) use ($academicClassId) {
                $query->where('academic_classes.id', $academicClassId);
            })
            ->orderBy('admission_number')
            ->get();
    }

    public function getStudentAttendanceRecords(
        int $studentId,
        Carbon $startDate,
        Carbon $endDate
    ): Collection {
        return Attendance::query()
            ->with('academicClass:id,name')
            ->where('student_id', $studentId)
            ->whereBetween('attendance_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ])
            ->orderBy('updated_at')
            ->orderBy('id')
            ->get()
            // Later (more recently marked) records overwrite earlier ones for the same day.
            ->keyBy(fn (Attendance $attendance) => $attendance->attendance_date->toDateString())
            ->sortKeysDesc()
            ->values();
    }
}
