<?php

namespace App\Services\Hifz;

use App\Contracts\StudentInterface;
use App\Exceptions\ApiException;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

/**
 * Read-only Hifz data for the signed-in student. Always uses the student
 * from the token and returns the same shapes as the per-student endpoints.
 */
class MyHifzService
{
    private const STUDENT_ROLE = 'Student';

    public function __construct(
        protected StudentInterface $studentRepository,
        protected HifzStatisticsService $statisticsService,
        protected QuranMapService $quranMapService,
        protected HifzSessionService $sessionService,
    ) {}

    /**
     * Profile + statistics + Quran map. A student with no records gets zero
     * statistics and every surah as not-started.
     *
     * @return array{student: array, statistics: array, quran_map_surahs: Collection, quran_map_statistics: array}
     */
    public function getMyHifz(User $user): array
    {
        $student = $this->resolveStudent($user);

        $surahs = $student
            ? $this->quranMapService->getStudentQuranMap($student->id)
            : $this->quranMapService->getEmptyQuranMap();

        return [
            'student' => $this->profile($user, $student),
            'statistics' => $student
                ? $this->statisticsService->getStudentHifzStatistics($student->id)
                : $this->emptyStatistics($surahs->count()),
            'quran_map_surahs' => $surahs,
            'quran_map_statistics' => $this->quranMapService->summarise($surahs),
        ];
    }

    /**
     * The student's sessions, newest first.
     */
    public function getMySessions(User $user, ?string $date, int $perPage, int $page = 1): LengthAwarePaginator
    {
        $student = $this->resolveStudent($user);

        if (!$student) {
            return new Paginator([], 0, $perPage, $page);
        }

        return $this->sessionService->paginateStudentSessions($student->id, $date, $perPage);
    }

    private function resolveStudent(User $user): ?Student
    {
        if (!$user->hasRole(self::STUDENT_ROLE)) {
            throw new ApiException(
                message: 'Only students can view their own Hifz.',
                status: 403
            );
        }

        return $this->studentRepository->findByUserId($user->id)?->loadMissing('academicClasses:id,name');
    }

    private function profile(User $user, ?Student $student): array
    {
        return [
            'name' => $user->name ?? '',
            'initials' => $user->initials ?? '',
            'image' => $user->image,
            'class_name' => $student?->academicClasses->first()?->name,
            'admission_number' => $student?->admission_number,
        ];
    }

    /**
     * Same shape as the Hifz statistics endpoint, all zero.
     */
    private function emptyStatistics(int $totalSurahs): array
    {
        return [
            'memorised' => 0,
            'in_progress' => 0,
            'remaining' => $totalSurahs,
            'surahs' => ['completed' => 0, 'total' => $totalSurahs, 'percentage' => 0],
            'pages' => ['completed' => 0, 'total' => 0, 'percentage' => 0],
        ];
    }
}
