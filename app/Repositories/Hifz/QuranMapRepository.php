<?php

namespace App\Repositories\Hifz;

use App\Contracts\Hifz\QuranMapInterface;
use App\Exceptions\ApiException;
use App\Models\Student;
use App\Models\Surah;
use Illuminate\Database\Eloquent\Collection;

class QuranMapRepository implements QuranMapInterface
{
    public function getStudentQuranMap(int $studentId): Collection
    {
        if (! Student::whereKey($studentId)->exists()) {
            throw new ApiException(
                message: 'Student not found.',
                status: 404
            );
        }

        return Surah::query()
            ->with([
                'studentProgress' => function ($query) use ($studentId) {
                    $query->where('student_id', $studentId);
                },
                'juzSurahRanges.juz',
            ])
            ->orderBy('number')
            ->get();
    }

    public function getEmptyQuranMap(): Collection
    {
        return Surah::query()
            ->with('juzSurahRanges.juz')
            ->orderBy('number')
            ->get()
            ->each(fn (Surah $surah) => $surah->setRelation('studentProgress', new Collection()));
    }
}