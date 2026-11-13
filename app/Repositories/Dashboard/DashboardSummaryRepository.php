<?php

namespace App\Repositories\Dashboard;

use App\Contracts\Dashboard\DashboardSummaryInterface;
use App\DTOs\Dashboard\DashboardSummaryDTO;
use App\Models\AcademicClass;
use App\Models\Attendance;
use App\Models\HifzSession;
use App\Models\Notice;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class DashboardSummaryRepository implements DashboardSummaryInterface
{
    private const TOTAL_SURAHS = 114;

    private const ON_TRACK_THRESHOLD = 70;

    public function getDashboardSummary(DashboardSummaryDTO $dto, ?array $classIds = null): array
    {
        return [
            'statistics' => $this->getStatistics($classIds),

            'hifz_overview' => $this->getHifzOverview(),

            'attendance_overview' => $this->getAttendanceOverview(
                $dto->attendancePeriod,
                $classIds
            ),

            'quran_progress_overview' => $this->getQuranProgressOverview(),

            'announcements' => $this->getAnnouncements(),
        ];
    }

    private function getStatistics(?array $classIds = null): array
    {
        $today = now()->startOfDay();
        $yesterday = $today->copy()->subDay();

        $totalStudents = Student::query()
            ->when($classIds !== null, function ($query) use ($classIds) {
                $query->whereHas('academicClasses', function ($q) use ($classIds) {
                    $q->whereIn('academic_classes.id', $classIds);
                });
            })
            ->count();

        $totalTeachers = Teacher::count();

        $studentsAddedThisMonth = Student::query()
            ->when($classIds !== null, function ($query) use ($classIds) {
                $query->whereHas('academicClasses', function ($q) use ($classIds) {
                    $q->whereIn('academic_classes.id', $classIds);
                });
            })
            ->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])
            ->count();

        $teachersAddedThisMonth = Teacher::query()
            ->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])
            ->count();

        $sessionsToday = HifzSession::query()
            ->when($classIds !== null, function ($query) use ($classIds) {
                $query->whereHas('student.academicClasses', function ($q) use ($classIds) {
                    $q->whereIn('academic_classes.id', $classIds);
                });
            })
            ->whereDate('session_date', $today)
            ->count();

        $sessionsYesterday = HifzSession::query()
            ->when($classIds !== null, function ($query) use ($classIds) {
                $query->whereHas('student.academicClasses', function ($q) use ($classIds) {
                    $q->whereIn('academic_classes.id', $classIds);
                });
            })
            ->whereDate('session_date', $yesterday)
            ->count();

        $currentWeekStart = now()->startOfWeek();
        $previousWeekStart = $currentWeekStart->copy()->subWeek();
        $previousWeekEnd = $currentWeekStart->copy()->subDay();

        $currentWeekAttendance = $this->getAttendanceAggregates(
            $currentWeekStart,
            now()->startOfDay(),
            $classIds
        );

        $previousWeekAttendance = $this->getAttendanceAggregates(
            $previousWeekStart,
            $previousWeekEnd,
            $classIds
        );

        $currentAttendanceRate = $this->calculateAttendanceRate(
            $currentWeekAttendance
        );

        $previousAttendanceRate = $this->calculateAttendanceRate(
            $previousWeekAttendance
        );

        return [
            'total_students' => $totalStudents,
            'students_added_this_month' => $studentsAddedThisMonth,

            'total_teachers' => $totalTeachers,
            'teachers_added_this_month' => $teachersAddedThisMonth,

            'hifz_sessions_today' => $sessionsToday,
            'sessions_vs_yesterday' => $sessionsToday - $sessionsYesterday,

            'attendance_rate' => $currentAttendanceRate,
            'attendance_change' => round(
                $currentAttendanceRate - $previousAttendanceRate,
                1
            ),
        ];
    }

    private function getHifzOverview(): Collection
    {
        return Student::query()
            ->whereHas('hifzSessions')
            ->with('user:id,name')
            ->withCount([
                'surahProgress as memorised_surahs_count' => function ($query) {
                    $query->where('status', 'memorised');
                },
            ])
            ->withMax('hifzSessions', 'session_date')
            ->orderByDesc('hifz_sessions_max_session_date')
            ->limit(4)
            ->get()
            ->map(function (Student $student) {
                $name = $student->user?->name ?? "Student #{$student->id}";

                $memorisedSurahs = $student->memorised_surahs_count;

                return [
                    'student_id' => $student->id,
                    'name' => $name,
                    'initials' => $this->getInitials($name),
                    'memorised_surahs' => $memorisedSurahs,
                    'total_surahs' => self::TOTAL_SURAHS,
                    'progress' => round(
                        ($memorisedSurahs / self::TOTAL_SURAHS) * 100,
                        1
                    ),
                    'last_session_date' => $student->hifz_sessions_max_session_date,
                ];
            });
    }

    private function getAttendanceOverview(string $period, ?array $classIds = null): array
    {
        $today = now()->startOfDay();

        if ($period === '7days') {
            $startDate = $today->copy()->subDays(6);
        } elseif ($period === '30days') {
            $startDate = $today->copy()->subDays(29);
        } else {
            $startDate = $today->copy()->startOfMonth()->subMonths(2);
        }

        $records = $this->getAttendanceAggregates(
            $startDate,
            $today,
            $classIds
        );

        $data = [];

        if ($period === '3months') {
            $months = CarbonPeriod::create(
                $startDate->copy()->startOfMonth(),
                '1 month',
                $today->copy()->startOfMonth()
            );

            foreach ($months as $month) {
                $monthPrefix = $month->format('Y-m');

                $monthRecords = $records->filter(
                    fn ($record, $date) => str_starts_with($date, $monthPrefix)
                );

                $data[] = [
                    'day' => strtoupper($month->format('M')),
                    'attendance' => $this->calculateAttendanceRate($monthRecords),
                ];
            }
        } else {
            $days = CarbonPeriod::create(
                $startDate,
                '1 day',
                $today
            );

            foreach ($days as $date) {
                $dateKey = $date->toDateString();
                $record = $records->get($dateKey);

                $total = $record?->total ?? 0;
                $attended = $record?->attended ?? 0;

                $data[] = [
                    'day' => $period === '7days'
                        ? strtoupper($date->format('D'))
                        : $date->format('d'),

                    'attendance' => $total > 0
                        ? round(($attended / $total) * 100, 1)
                        : 0,
                ];
            }
        }

        return [
            'period' => $period,
            'data' => $data,
        ];
    }

    private function getQuranProgressOverview(): Collection
    {
        return AcademicClass::query()
            ->where('is_active', true)
            ->with([
                'students.surahProgress' => function ($query) {
                    $query->where('status', 'memorised');
                },
            ])
            ->orderBy('name')
            ->get()
            ->map(function (AcademicClass $academicClass) {
                $students = $academicClass->students;

                $studentCount = $students->count();

                $totalMemorisedSurahs = $students->sum(
                    fn ($student) => $student->surahProgress->count()
                );

                $progress = $studentCount > 0
                    ? round(
                        ($totalMemorisedSurahs /
                            ($studentCount * self::TOTAL_SURAHS)) * 100,
                        1
                    )
                    : 0;

                return [
                    'id' => $academicClass->id,
                    'title' => $academicClass->name,
                    'student_count' => $studentCount,
                    'progress' => $progress,
                    'status' => $progress >= self::ON_TRACK_THRESHOLD
                        ? 'on-track'
                        : 'needs-attention',
                ];
            });
    }

    private function getAnnouncements(): Collection
    {
        return Notice::query()
            ->where('is_active', true)
            ->latest()
            ->limit(3)
            ->get([
                'id',
                'title',
                'message',
                'created_at',
            ]);
    }

    private function getAttendanceAggregates(
        Carbon $startDate,
        Carbon $endDate,
        ?array $classIds = null
    ): Collection {
        return Attendance::query()
            ->when($classIds !== null, function ($query) use ($classIds) {
                $query->whereHas('student.academicClasses', function ($q) use ($classIds) {
                    $q->whereIn('academic_classes.id', $classIds);
                });
            })
            ->whereBetween('attendance_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ])
            ->select('attendance_date')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw(
                "SUM(CASE WHEN status IN ('present', 'late') THEN 1 ELSE 0 END) as attended"
            )
            ->groupBy('attendance_date')
            ->get()
            ->keyBy(
                fn ($record) => Carbon::parse(
                    $record->attendance_date
                )->toDateString()
            );
    }

    private function calculateAttendanceRate(Collection $records): float
    {
        $total = $records->sum('total');
        $attended = $records->sum('attended');

        return $total > 0
            ? round(($attended / $total) * 100, 1)
            : 0;
    }

    private function getInitials(string $name): string
    {
        return collect(explode(' ', trim($name)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('');
    }
}
