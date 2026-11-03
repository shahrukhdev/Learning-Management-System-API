<?php

namespace App\Contracts\Hifz;

use App\DTOs\Hifz\StoreHifzSessionDTO;
use App\DTOs\Hifz\UpdateHifzSessionDTO;
use App\Models\HifzSession;
use Illuminate\Database\Eloquent\Collection;

interface HifzSessionInterface
{
    public function getStudentSessions(int $studentId, ?string $date): Collection;

    public function createSession(StoreHifzSessionDTO $dto): HifzSession;

    public function getSession(int $id, int $studentId): HifzSession;

    public function updateSession(
        int $id,
        int $studentId,
        UpdateHifzSessionDTO $dto
    ): HifzSession;

    public function deleteSession(int $id, int $studentId): void;
}