<?php

namespace App\Services\Hifz;

use App\Contracts\Hifz\QuranMapInterface;
use Illuminate\Database\Eloquent\Collection;

class QuranMapService
{
    public function __construct(
        protected QuranMapInterface $repository
    ) {}

    public function getStudentQuranMap(int $studentId): Collection
    {
        return $this->repository->getStudentQuranMap($studentId);
    }

    public function getEmptyQuranMap(): Collection
    {
        return $this->repository->getEmptyQuranMap();
    }

    /**
     * Counts shown above the Quran map.
     *
     * @return array{total_surahs: int, memorised: int, in_progress: int, remaining: int}
     */
    public function summarise(Collection $surahs): array
    {
        $memorised = $surahs->filter(
            fn ($surah) => $surah->studentProgress->first()?->status === 'memorised'
        )->count();

        $inProgress = $surahs->filter(
            fn ($surah) => $surah->studentProgress->first()?->status === 'progress'
        )->count();

        return [
            'total_surahs' => $surahs->count(),
            'memorised' => $memorised,
            'in_progress' => $inProgress,
            'remaining' => $surahs->count() - $memorised - $inProgress,
        ];
    }
}