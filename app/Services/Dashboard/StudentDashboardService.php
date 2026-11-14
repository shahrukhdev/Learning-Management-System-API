<?php

namespace App\Services\Dashboard;

use App\Contracts\Dashboard\StudentDashboardInterface;
use App\DTOs\Dashboard\DashboardSummaryDTO;
use App\Enums\AttendancePeriod;
use App\Enums\AttendanceStatus;
use App\Exceptions\ApiException;
use App\Models\Student;
use App\Models\User;
use App\Services\Hifz\HifzStatisticsService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class StudentDashboardService
{
    private const STUDENT_ROLE = 'Student';

    /** Manzil entry statuses / strengths that mean the portion needs revision. */
    private const NEEDS_REVISION_STATUSES = ['needs-revision', 'repeat'];
    private const NEEDS_REVISION_STRENGTHS = ['weak'];

    public function __construct(
        protected StudentDashboardInterface $repository,
        protected HifzStatisticsService $hifzStatisticsService,
    ) {}

    /**
     * Dashboard data for the signed-in student. Every section is always
     * present; sections without data are null (or have an empty data array).
     */
    public function getStudentSummary(User $user, DashboardSummaryDTO $dto): array
    {
        if (!$user->hasRole(self::STUDENT_ROLE)) {
            throw new ApiException(
                message: 'Only students can access the student dashboard.',
                status: 403
            );
        }

        $student = $this->repository->findStudentByUserId($user->id);

        return [
            'student' => $this->studentSection($user, $student),
            'today_attendance' => $student ? $this->todayAttendanceSection($student) : null,
            'new_sabaq' => $student ? $this->newSabaqSection($student) : null,
            'manzil' => $student ? $this->manzilSection($student) : null,
            'hifz_progress' => $student ? $this->hifzProgressSection($student) : null,
            'attendance_overview' => $this->attendanceOverviewSection($student, $dto->attendancePeriod),
        ];
    }

    private function studentSection(User $user, ?Student $student): array
    {
        return [
            'name' => $user->name ?? '',
            'initials' => $user->initials ?? '',
            'class_name' => $student?->academicClasses->first()?->name,
            'admission_number' => $student?->admission_number,
            'image' => $user->image,
        ];
    }

    private function todayAttendanceSection(Student $student): array
    {
        $attendance = $this->repository->getTodayAttendance($student->id);

        return [
            'status' => $attendance?->status->value,
            'marked_at' => $attendance?->updated_at?->format('H:i'),
        ];
    }

    private function newSabaqSection(Student $student): ?array
    {
        $session = $this->repository->getLatestNewSabaqSession($student->id);

        if (!$session) {
            return null;
        }

        $first = $session->newSabaqEntries->first();
        $last = $session->newSabaqEntries->last();

        $fromSurah = $first->fromSurah?->name ?? '';
        $toSurah = $last->toSurah?->name ?? '';

        // "Surah Al-Mulk 1–10" or "Al-Mulk 1 – Al-Qalam 5"
        $label = $fromSurah === $toSurah
            ? "Surah {$fromSurah} {$first->from_ayah}–{$last->to_ayah}"
            : "{$fromSurah} {$first->from_ayah} – {$toSurah} {$last->to_ayah}";

        return [
            'label' => $label,
            'assigned_date' => $session->session_date?->format('Y-m-d'),
        ];
    }

    private function manzilSection(Student $student): ?array
    {
        $session = $this->repository->getLatestManzilSession($student->id);

        if (!$session) {
            return null;
        }

        $entries = $session->manzilEntries;

        $fromSurah = $entries->first()->fromSurah?->name ?? '';
        $toSurah = $entries->last()->toSurah?->name ?? '';

        // "Surah Al-Baqarah" or "Al-Fatihah – Al-Baqarah"
        $surahLabel = $fromSurah === $toSurah
            ? "Surah {$fromSurah}"
            : "{$fromSurah} – {$toSurah}";

        // Juz covered: from the first Juz of the starting surah to the last Juz
        // of the ending surah, across all manzil entries of the session.
        $fromJuz = $entries
            ->flatMap(fn ($entry) => $entry->fromSurah?->juzSurahRanges ?? [])
            ->pluck('juz.number')
            ->filter()
            ->min();

        $toJuz = $entries
            ->flatMap(fn ($entry) => $entry->toSurah?->juzSurahRanges ?? [])
            ->pluck('juz.number')
            ->filter()
            ->max();

        // "Juz 1" or "Juz 1–3"; falls back to the surah range if Juz data is missing.
        $label = match (true) {
            $fromJuz === null || $toJuz === null => $surahLabel,
            $fromJuz === $toJuz => "Juz {$fromJuz}",
            default => "Juz {$fromJuz}–{$toJuz}",
        };

        $needsRevision = $entries->contains(
            fn ($entry) => in_array($entry->status, self::NEEDS_REVISION_STATUSES, true)
                || in_array($entry->strength, self::NEEDS_REVISION_STRENGTHS, true)
        );

        return [
            'label' => $label,
            'surahs' => $surahLabel,
            'from_juz' => $fromJuz,
            'to_juz' => $toJuz,
            'status' => $needsRevision ? 'needs-revision' : 'on-track',
            'assigned_date' => $session->session_date?->format('Y-m-d'),
        ];
    }

    /**
     * Same numbers as the Hifz statistics screen.
     */
    private function hifzProgressSection(Student $student): array
    {
        $statistics = $this->hifzStatisticsService->getStudentHifzStatistics($student->id);

        return [
            'memorised_pages' => (int) $statistics['pages']['completed'],
            'total_pages' => (int) $statistics['pages']['total'],
            'memorised_surahs' => (int) $statistics['surahs']['completed'],
            'total_surahs' => (int) $statistics['surahs']['total'],
            // Pages-based, for the "Pages Memorised" card.
            'percentage' => (float) $statistics['pages']['percentage'],
            // Surah-based, same as Student Progress and the Hifz Overview.
            'surah_percentage' => (float) $statistics['surahs']['percentage'],
        ];
    }

    private function attendanceOverviewSection(?Student $student, string $period): array
    {
        $today = now()->startOfDay();
        $attendancePeriod = AttendancePeriod::fromValue($period);
        $period = $attendancePeriod->value;
        $startDate = $attendancePeriod->startDate($today);

        $statuses = $student
            ? $this->repository->getAttendanceStatusesByDate($student->id, $startDate, $today)
            : collect();

        $data = $period === '3months'
            ? $this->monthlyAttendance($statuses, $startDate, $today)
            : $this->dailyAttendance($statuses, $startDate, $today, $period);

        return [
            'period' => $period,
            'data' => $data,
            'summary' => $this->attendanceCounts($statuses),
        ];
    }

    /**
     * One item per day: the student's status that day (null = not marked).
     */
    private function dailyAttendance(Collection $statuses, Carbon $startDate, Carbon $endDate, string $period): array
    {
        $data = [];

        foreach (CarbonPeriod::create($startDate, '1 day', $endDate) as $date) {
            $data[] = [
                'label' => $period === '7days'
                    ? strtoupper($date->format('D'))
                    : $date->format('d'),
                'date' => $date->toDateString(),
                'status' => $statuses->get($date->toDateString()),
            ];
        }

        return $data;
    }

    /**
     * One item per month with that month's counts and attendance rate.
     */
    private function monthlyAttendance(Collection $statuses, Carbon $startDate, Carbon $endDate): array
    {
        $data = [];

        foreach (CarbonPeriod::create($startDate->copy()->startOfMonth(), '1 month', $endDate->copy()->startOfMonth()) as $month) {
            $prefix = $month->format('Y-m');

            $monthStatuses = $statuses->filter(
                fn ($status, $date) => str_starts_with($date, $prefix)
            );

            $data[] = [
                'label' => strtoupper($month->format('M')),
                'month' => $prefix,
                ...$this->attendanceCounts($monthStatuses),
            ];
        }

        return $data;
    }

    /**
     * Counts + rate; present and late both count as attended (same as the dashboard).
     *
     * @return array{present: int, late: int, absent: int, attendance_rate: float}
     */
    private function attendanceCounts(Collection $statuses): array
    {
        $counts = $statuses->countBy();

        $present = (int) $counts->get(AttendanceStatus::PRESENT->value, 0);
        $late = (int) $counts->get(AttendanceStatus::LATE->value, 0);
        $absent = (int) $counts->get(AttendanceStatus::ABSENT->value, 0);
        $total = $present + $late + $absent;

        return [
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'attendance_rate' => $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0,
        ];
    }
}
