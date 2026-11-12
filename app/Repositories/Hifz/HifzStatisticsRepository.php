<?php

namespace App\Repositories\Hifz;

use App\Contracts\Hifz\HifzStatisticsInterface;
use App\Exceptions\ApiException;
use App\Models\HifzSession;
use App\Models\HifzSetting;
use App\Models\Student;
use App\Models\Surah;

class HifzStatisticsRepository implements HifzStatisticsInterface
{
    public function getStudentHifzStatistics(int $studentId): array
    {
        $student = Student::query()
            ->whereKey($studentId)
            ->withCount([
                'surahProgress as memorised_surahs_count' => function ($query) {
                    $query->where('status', 'memorised');
                },
                'surahProgress as in_progress_surahs_count' => function ($query) {
                    $query->where('status', 'progress');
                },
            ])
            ->first();

        if (!$student) {
            throw new ApiException(
                message: 'Student not found.',
                status: 404
            );
        }

        $totalSurahs = Surah::query()->count();

        $memorised = $student->memorised_surahs_count;
        $inProgress = $student->in_progress_surahs_count;
        $remaining = max(0, $totalSurahs - $memorised - $inProgress);

        $surahPercentage = $totalSurahs > 0
            ? round(($memorised / $totalSurahs) * 100, 1)
            : 0;

        $totalPages = HifzSetting::query()
            ->where('course_id', $student->course_id)
            ->value('total_pages') ?? 670;

        $latestSession = HifzSession::query()
            ->where('student_id', $studentId)
            ->with('oldSabaqEntries')
            ->orderByDesc('session_date')
            ->orderByDesc('created_at')
            ->first();

        $completedPages = $latestSession?->oldSabaqEntries
            ->last()?->page ?? 0;

        $pagePercentage = $totalPages > 0
            ? round(($completedPages / $totalPages) * 100, 1)
            : 0;

        return [
            'memorised' => $memorised,
            'in_progress' => $inProgress,
            'remaining' => $remaining,
            'surahs' => [
                'completed' => $memorised,
                'total' => $totalSurahs,
                'percentage' => $surahPercentage,
            ],
            'pages' => [
                'completed' => $completedPages,
                'total' => $totalPages,
                'percentage' => $pagePercentage,
            ],
        ];
    }
}
