<?php

namespace App\Repositories\Dashboard;

use App\Contracts\Dashboard\StudentDashboardInterface;
use App\Models\Attendance;
use App\Models\HifzSession;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StudentDashboardRepository implements StudentDashboardInterface
{
    public function findStudentByUserId(int $userId): ?Student
    {
        return Student::query()
            ->with(['user', 'academicClasses:id,name'])
            ->where('user_id', $userId)
            ->first();
    }

    public function getTodayAttendance(int $studentId): ?Attendance
    {
        return Attendance::query()
            ->where('student_id', $studentId)
            ->whereDate('attendance_date', now()->toDateString())
            ->latest('updated_at')
            ->first();
    }

    public function getLatestNewSabaqSession(int $studentId): ?HifzSession
    {
        return HifzSession::query()
            ->where('student_id', $studentId)
            ->whereHas('newSabaqEntries')
            ->with([
                'newSabaqEntries' => fn ($query) => $query->orderBy('id'),
                'newSabaqEntries.fromSurah:id,name',
                'newSabaqEntries.toSurah:id,name',
            ])
            ->orderByDesc('session_date')
            ->orderByDesc('id')
            ->first();
    }

    public function getLatestManzilSession(int $studentId): ?HifzSession
    {
        return HifzSession::query()
            ->where('student_id', $studentId)
            ->whereHas('manzilEntries')
            ->with([
                'manzilEntries' => fn ($query) => $query->orderBy('id'),
                'manzilEntries.fromSurah:id,name',
                'manzilEntries.toSurah:id,name',
            ])
            ->orderByDesc('session_date')
            ->orderByDesc('id')
            ->first();
    }

    public function getAttendanceStatusesByDate(int $studentId, Carbon $startDate, Carbon $endDate): Collection
    {
        // A student in several classes can have one record per class per day;
        // the most recently marked one wins.
        return Attendance::query()
            ->where('student_id', $studentId)
            ->whereBetween('attendance_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ])
            ->orderBy('updated_at')
            ->get(['attendance_date', 'status', 'updated_at'])
            ->mapWithKeys(fn (Attendance $attendance) => [
                $attendance->attendance_date->toDateString() => $attendance->status->value,
            ]);
    }
}
