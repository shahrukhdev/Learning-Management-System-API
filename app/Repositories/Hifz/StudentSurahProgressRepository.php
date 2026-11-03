<?php

namespace App\Repositories\Hifz;

use App\Contracts\Hifz\StudentSurahProgressInterface;
use App\DTOs\Hifz\StoreStudentSurahProgressDTO;
use App\DTOs\Hifz\UpdateStudentSurahProgressDTO;
use App\Exceptions\ApiException;
use App\Models\StudentSurahProgress;
use Illuminate\Database\Eloquent\Collection;

class StudentSurahProgressRepository implements StudentSurahProgressInterface
{
    public function getStudentProgress(int $studentId): Collection
    {
        return StudentSurahProgress::query()
            ->with('surah')
            ->where('student_id', $studentId)
            ->get()
            ->sortBy('surah.number')
            ->values();
    }

    public function createProgress(StoreStudentSurahProgressDTO $dto): StudentSurahProgress
    {
        $progress = StudentSurahProgress::create($dto->toArray());

        return $progress->load('surah');
    }

    public function getProgress(int $id): StudentSurahProgress
    {
        $progress = StudentSurahProgress::query()
            ->with('surah')
            ->find($id);

        if (! $progress) {
            throw new ApiException(message: 'Student Surah progress not found.', status: 404);
        }

        return $progress;
    }

    public function updateProgress(
        int $id,
        UpdateStudentSurahProgressDTO $dto
    ): StudentSurahProgress {
        $progress = $this->getProgress($id);

        $progress->update($dto->toArray());

        return $progress->load('surah');
    }

    public function deleteProgress(int $id): void
    {
        $progress = $this->getProgress($id);

        $progress->delete();
    }
}