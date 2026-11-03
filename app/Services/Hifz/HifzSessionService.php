<?php

namespace App\Services\Hifz;

use App\Contracts\Hifz\HifzSessionInterface;
use App\DTOs\Hifz\StoreHifzSessionDTO;
use App\DTOs\Hifz\UpdateHifzSessionDTO;
use App\Models\HifzSession;
use Illuminate\Database\Eloquent\Collection;

class HifzSessionService
{
    public function __construct(
        protected HifzSessionInterface $repository
    ) {}

    public function getStudentSessions(int $studentId, ?string $date): Collection
    {
        return $this->repository->getStudentSessions($studentId, $date);
    }

    public function createSession(StoreHifzSessionDTO $dto): HifzSession
    {
        return $this->repository->createSession($dto);
    }

    public function getSession(int $id, int $studentId): HifzSession
    {
        return $this->repository->getSession($id, $studentId);
    }

    public function updateSession(
        int $id,
        int $studentId,
        UpdateHifzSessionDTO $dto
    ): HifzSession {
        return $this->repository->updateSession($id, $studentId, $dto);
    }

    public function deleteSession(int $id, int $studentId): void
    {
        $this->repository->deleteSession($id, $studentId);
    }
}