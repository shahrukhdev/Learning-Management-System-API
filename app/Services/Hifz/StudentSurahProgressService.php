<?php

namespace App\Services\Hifz;

use App\Contracts\Hifz\StudentSurahProgressInterface;
use App\DTOs\Hifz\StoreStudentSurahProgressDTO;
use App\DTOs\Hifz\UpdateStudentSurahProgressDTO;
use App\Models\StudentSurahProgress;
use Illuminate\Database\Eloquent\Collection;

class StudentSurahProgressService
{
    public function __construct(
        protected StudentSurahProgressInterface $repository
    ) {}

    public function getStudentProgress(int $studentId): Collection
    {
        return $this->repository->getStudentProgress($studentId);
    }

    public function createProgress(StoreStudentSurahProgressDTO $dto): StudentSurahProgress
    {
        return $this->repository->createProgress($dto);
    }

    public function getProgress(int $id): StudentSurahProgress
    {
        return $this->repository->getProgress($id);
    }

    public function updateProgress(
        int $id,
        UpdateStudentSurahProgressDTO $dto
    ): StudentSurahProgress {
        return $this->repository->updateProgress($id, $dto);
    }

    public function deleteProgress(int $id): void
    {
        $this->repository->deleteProgress($id);
    }
}