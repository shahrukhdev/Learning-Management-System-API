<?php

namespace App\Contracts\Hifz;

use App\DTOs\Hifz\StoreStudentSurahProgressDTO;
use App\DTOs\Hifz\UpdateStudentSurahProgressDTO;
use App\Models\StudentSurahProgress;
use Illuminate\Database\Eloquent\Collection;

interface StudentSurahProgressInterface
{
    public function getStudentProgress(int $studentId): Collection;

    public function createProgress(StoreStudentSurahProgressDTO $dto): StudentSurahProgress;

    public function getProgress(int $id): StudentSurahProgress;

    public function updateProgress(int $id, UpdateStudentSurahProgressDTO $dto): StudentSurahProgress;

    public function deleteProgress(int $id): void;
}