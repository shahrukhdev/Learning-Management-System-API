
<?php

namespace App\Repositories\Hifz;

use App\Contracts\Hifz\HifzSessionInterface;
use App\DTOs\Hifz\StoreHifzSessionDTO;
use App\DTOs\Hifz\UpdateHifzSessionDTO;
use App\Exceptions\ApiException;
use App\Models\HifzSession;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class HifzSessionRepository implements HifzSessionInterface
{
    private function sessionRelations(): array
    {
        return [
            'oldSabaqEntries.fromSurah',
            'oldSabaqEntries.toSurah',
            'manzilEntries.fromSurah',
            'manzilEntries.toSurah',
            'newSabaqEntries.fromSurah',
            'newSabaqEntries.toSurah',
        ];
    }

    public function getStudentSessions(int $studentId, ?string $date): Collection
    {
        return HifzSession::query()
            ->with($this->sessionRelations())
            ->where('student_id', $studentId)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('session_date', $date);
            })
            ->orderByDesc('session_date')
            ->orderByDesc('created_at')
            ->get();
    }

    public function createSession(StoreHifzSessionDTO $dto): HifzSession
    {
        return DB::transaction(function () use ($dto) {
            $session = HifzSession::create($dto->toArray());

            if (! empty($dto->old_sabaq)) {
                $session->oldSabaqEntries()->createMany($dto->old_sabaq);
            }

            if (! empty($dto->manzil)) {
                $session->manzilEntries()->createMany($dto->manzil);
            }

            if (! empty($dto->new_sabaq)) {
                $session->newSabaqEntries()->createMany($dto->new_sabaq);
            }

            return $session->load($this->sessionRelations());
        });
    }

    public function getSession(int $id, int $studentId): HifzSession
    {
        $session = HifzSession::query()
            ->with($this->sessionRelations())
            ->where('student_id', $studentId)
            ->find($id);

        if (! $session) {
            throw new ApiException(
                message: 'Hifz session not found.',
                status: 404
            );
        }

        return $session;
    }

    public function updateSession(
        int $id,
        int $studentId,
        UpdateHifzSessionDTO $dto
    ): HifzSession {
        return DB::transaction(function () use ($id, $studentId, $dto) {
            $session = $this->getSession($id, $studentId);

            $session->update($dto->toArray());

            $session->oldSabaqEntries()->delete();
            $session->manzilEntries()->delete();
            $session->newSabaqEntries()->delete();

            if (! empty($dto->old_sabaq)) {
                $session->oldSabaqEntries()->createMany($dto->old_sabaq);
            }

            if (! empty($dto->manzil)) {
                $session->manzilEntries()->createMany($dto->manzil);
            }

            if (! empty($dto->new_sabaq)) {
                $session->newSabaqEntries()->createMany($dto->new_sabaq);
            }

            return $session->load($this->sessionRelations());
        });
    }

    public function deleteSession(int $id, int $studentId): void
    {
        DB::transaction(function () use ($id, $studentId) {
            $session = $this->getSession($id, $studentId);

            $session->delete();
        });
    }
}
