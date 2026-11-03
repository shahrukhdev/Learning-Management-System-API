
<?php

namespace App\Repositories\Hifz;

use App\Contracts\Hifz\HifzStatisticsInterface;
use App\Exceptions\ApiException;
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

        $percentage = $totalSurahs > 0
            ? round(($memorised / $totalSurahs) * 100, 1)
            : 0;

        return [
            'memorised' => $memorised,
            'in_progress' => $inProgress,
            'remaining' => $remaining,
            'surahs' => [
                'completed' => $memorised,
                'total' => $totalSurahs,
                'percentage' => $percentage,
            ],
        ];
    }
}
